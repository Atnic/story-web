<template>
    <div>
        <v-toolbar flat fixed color="#fff">
            <v-icon @click="$router.go(-1)">keyboard_backspace</v-icon>
            <v-toolbar-title>
                Story
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <div v-if="post.user_id == $store.state.auth.user.id">
                <v-menu>
                    <v-icon
                        slot="activator"
                        flat
                        ripple
                        medium>more_vert
                    </v-icon>
                    <v-list>
                        <v-list-tile @click="compose('edit', 'story', post.post)" >
                            <v-list-tile-title>Edit</v-list-tile-title>
                        </v-list-tile>
                        <v-divider></v-divider>
                        <v-list-tile @click="deleteDialog = true" >
                            <v-list-tile-title>
                                <span slot="activator" class="red--text" flat>Delete</span>
                                <v-dialog v-model="deleteDialog" persistent max-width="290">
                                    <v-card v-if="!deleting">
                                        <v-card-title class="subheading">Delete your story?</v-card-title>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn flat @click="deleteDialog = false">No</v-btn>
                                            <v-btn color="red" flat @click="deletePost">Sure</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                    <v-card v-else class="text-xs-center pa-4">
                                        <h2 class="black--text subheading mb-4">Deleting</h2>
                                        <v-progress-circular
                                            :size="50"
                                            color="primary"
                                            indeterminate
                                        ></v-progress-circular>
                                    </v-card>
                                </v-dialog>
                            </v-list-tile-title>
                        </v-list-tile>
                    </v-list>
                </v-menu>
            </div>
        </v-toolbar>
        <div class="show-post">
            <app-loading v-if="!notLoading" style="margin-top: 64px;"></app-loading>
            <v-layout v-if="notLoading" class="content" column>
                <v-layout column class="post">
                    <v-layout align-center>
                        <v-avatar size="48px" class="mr-2" @click="$router.push({ name: 'User', params: { id: post.user_id } })">
                            <img :src="post.user.email ? gravatar(post.user.email) : 'undefined'" alt="avatar">
                        </v-avatar>
                        <v-layout column shrink @click="$router.push({ name: 'User', params: { id: post.user_id } })">
                            <h3>{{ post.user.name }}</h3>
                            <span>{{ getPostTime(post.created_at) }} {{ post.created_at !== post.updated_at ? '(edited)' : null }}</span>
                        </v-layout>
                    </v-layout>
                    <div class="mt-2 mb-2 item-content markdown-body" style="word-break: break-word;" v-html="$store.getters.markedText(text)" ></div>
                    <!-- <div v-if="isGiphy" class="giphy-container ma-0">
                        <img :src="giphyURL" style="width: 100%; border-radius: 4px; display: block;">
                    </div> -->
                </v-layout>
                <v-divider></v-divider>
                <v-layout pa-2 align-center justify-center>
                    <v-icon medium>chat_bubble_outline</v-icon>&nbsp;{{ comments.length }} comment{{ comments.length > 1 ? 's' : null }}
                </v-layout>
                <v-divider></v-divider>
                <comment-list
                    :key="commentsKey"
                    :comments="comments"
                    @success="fetchComment()"
                    @update-comment-item="compose('edit', 'comment', $event.body, $event.id)"></comment-list>
            </v-layout>
        </div>
        <app-composer
            v-if="composer.show"
            :composer="composer"
            @close-composer="composer.show = false"
            @success="success($event)">
        </app-composer>
        <v-btn color="blue" dark medium fixed bottom right fab @click="compose('compose', 'comment')">
            <v-icon>add_comment</v-icon>
        </v-btn>
    </div>
</template>

<script>
    import appLoading from '../loading'
    import commentList from '../comment/commentList'

    export default {
        components: {
            appLoading,
            commentList
        },

        data() {
            return {
                load: {
                    post: true,
                    comments: true
                },
                post: {},
                postId: '',
                comments: [],
                loading: false,
                deleteDialog: false,
                deleting: false,
                text: '',
                giphyURL: '',
                composer: {
                    postId: '',
                    commentId: '',
                    show: false,
                    type: '',
                    action: '',
                    isGiphy: ''
                },
                commentsKey: 0
            }
        },

        methods: {
            compose(action, type, body, commentId) {
                this.$store.state.editContent = body
                this.composer = {
                    show: true,
                    action,
                    type,
                    postId: this.postId,
                    commentId
                }

                if (action == 'edit') {
                    // if (body.match(/^\/giphy\shttps:\/\/.+\.gif\s+.+/)) {
                    //     let temp = body.split(' ')
                    //     temp.splice(0, 2)
                    //     this.$store.state.editContent = body.split(' ')[0] + ' ' + temp.join(' ')
                    //     this.$store.state.giphyURL = body.split(' ')[1]
                    // } else {
                    this.$store.state.editContent = body
                    // }
                } else {
                    this.$store.state.editContent = ''
                    this.$store.state.giphyURL = ''
                }
            },

            getPostTime(date) {
                return this.$store.getters.getTime(date)
            },

            gravatar(email) {
                return this.$store.getters.gravatar(email)
            },

            deletePost() {
                this.deleting = true
                this.axios.delete('/posts/' + this.postId, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(() => {
                        this.deleting = false
                        this.$router.go(-1)
                    })
                    .catch(err => {
                        this.deleting = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            async fetchPost() {
                this.load.post = true
                this.axios.get('/posts/' + this.postId + '?with=user', {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(r => r.data)
                    .then(post => post.data)
                    .then(post => {
                        this.post = post
                        // if (this.isGiphy) {
                        //     let temp = post.post.split(' ')
                        //     temp.splice(0, 2)
                        //     this.text = '<span class="grey--text">' + post.post.split(' ')[0] + '</span> ' + temp.join(' ')
                        //     this.giphyURL = post.post.split(' ')[1]
                        // } else {
                        this.text = post.post
                        // }
                        this.axios.get('/users/' + post.user_id, {
                            headers: {
                                'Authorization': 'Bearer ' + this.$store.state.auth.token
                            }
                        })
                            .then(r => r.data.data)
                            .then(user => {
                                this.post.user = user
                                this.load.post = false
                            })
                            .catch(err => {
                                this.load.post = false
                                this.$store.commit('displayMessage', err.message)
                            })
                    })
                    .catch(err => {
                        this.load.post = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            async fetchComment() {
                this.composer.show = false
                this.load.comment = true
                this.axios.get('/posts/' + this.postId + '/comments?with=user', {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(r => r.data)
                    .then(comments => {
                        this.comments = []
                        this.comments = comments.data
                        this.load.comments = false
                    })
                    .catch(err => {
                        this.load.comments = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            success(e) {
                if (e == 'edit post') this.fetchPost()
                else {
                    this.commentsKey += 1
                    this.fetchComment()
                }
            }
        },

        created() {
            this.postId = this.$router.currentRoute.params.id
            this.fetchPost()
            this.fetchComment()
        },

        computed: {
            notLoading() {
                let notLoad = !this.load.post && !this.load.comments
                this.$store.state.loadContent = !notLoad
                return !this.$store.state.loadContent
            },

            isGiphy() {
                return this.post.post.match(/^\/giphy\shttps:\/\/.+\.gif\s+.+/);
            }
        }
    }
</script>

<style>
    .grey {
        color: #e1e1e1;
    }

    .post {
        padding: 8px 5px;
    }

    .show-post {
        padding-bottom: 48px;
    }

    .giphy-container {
        background-color: #eee;
        border-radius: 4px;
        min-height: 200px;
    }
</style>

