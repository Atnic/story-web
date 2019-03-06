<template>
    <div>
        <v-toolbar flat fixed>
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
                        <v-list-tile @click="editPost" >
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
        <app-loading class="content" v-if="!notLoading"></app-loading>
        <v-layout v-if="notLoading" class="post-content content" column>
            <v-layout align-center class="post">
                <v-avatar size="48px" class="mr-2" @click="$router.push({ name: 'User', params: { id: post.user_id } })">
                    <img :src="post.user.email ? gravatar(post.user.email) : 'undefined'" alt="avatar">
                </v-avatar>
                <v-layout column shrink @click="$router.push({ name: 'User', params: { id: post.user_id } })">
                    <h3>{{ post.user.name }}</h3>
                    <span>{{ getPostTime(post.created_at) }} {{ post.created_at !== post.updated_at ? '(edited)' : null }}</span>
                </v-layout>
            </v-layout>
            <p class="p-show mt-3">{{ post.post }}</p>
            <v-divider class="mt-2"></v-divider>
                <v-layout pa-2 align-center justify-center>
                    <v-icon medium>chat_bubble_outline</v-icon>&nbsp;{{ comments.length }} comment{{ comments.length > 1 ? 's' : null }}
                </v-layout>
            <v-divider class="mb-2"></v-divider>
            <v-layout column>
                <comment-list :comments="comments" v-on:update-comment="fetchComment"></comment-list>
            </v-layout>
        </v-layout>
        <v-dialog class="dialog" v-model="createComment" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-toolbar flat>
                <v-icon @click="loading ? null : createComment = false">close</v-icon>
                <v-toolbar-title>
                    Create comment
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn v-if="comment" :loading="loading" @click="addComment">Comment</v-btn>
            </v-toolbar>
            <v-layout class="post-input">
                <v-textarea
                    label="Create your comment"
                    rows="3"
                    v-model="comment"
                    auto-grow
                    :disabled="loading">
                </v-textarea>
            </v-layout>
        </v-dialog>
        <v-dialog v-if="post.user_id == $store.state.auth.user.id" class="dialog" v-model="editDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-toolbar flat>
                <v-icon @click="loading ? null : resetPost()">close</v-icon>
                <v-toolbar-title>
                    Edit story
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn v-if="edit !== post.post && edit.length > 0" :loading="loading" @click="updatePost">Edit</v-btn>
            </v-toolbar>
            <v-layout class="post-input">
                <v-textarea
                    label="tulis post anda"
                    rows="3"
                    v-model="edit"
                    auto-grow
                    :disabled="loading">
                </v-textarea>
            </v-layout>
        </v-dialog>
        <v-btn
            color="blue"
            dark
            medium
            fixed
            bottom
            right
            fab
            v-show="!createComment"
            @click="createComment = true"
            >
            <v-icon>add_comment</v-icon>
        </v-btn>
        <app-snackbar></app-snackbar>
    </div>
</template>

<script>
import appLoading from '../loading'
import commentList from '../comment/commentList'
import appSnackbar from '../snackbar'
import md5 from 'md5'
import moment from 'moment'

export default {
    components: {
        appLoading,
        commentList,
        appSnackbar
    },

    data() {
        return {
            createComment: false,
            load: {
                post: true,
                comments: true
            },
            editDialog: false,
            post: {},
            postId: '',
            comments: [],
            comment: '',
            edit: '',
            loading: false,
            deleteDialog: false,
            deleting: false
        }
    },

    methods: {
        getPostTime(date) {
            moment.locale('id')
            return moment.utc(date).local().fromNow()
        },

        gravatar(email) {
            return 'https://www.gravatar.com/avatar/' + md5(email) + '?d=mp'
        },
        
        editPost() {
            this.editDialog = true
            this.edit = this.post.post
        },

        resetPost() {
            this.editDialog = false
            this.edit = this.post.post
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
                this.$router.push({ name: 'Home' })
            })
            .catch(err => {
                this.deleting = false
                this.$store.commit('displayMessage', err.message)
            })
        },

        async updatePost() {
            this.loading = true
            this.axios.put('/posts/' + this.postId, {
                post: this.edit
            }, {
                headers: {
                    'Authorization': 'Bearer ' + this.$store.state.auth.token
                }
            })
            .then(() => {
                this.edit = ''
                this.loading = false
                this.editDialog = false
                this.fetchPost()
                    .catch(err => this.$store.commit('displayMessage', err.message))
                this.$router.push({ name: 'Show', params: { id: this.postId }})
            })
            .catch(err => this.$store.commit('displayMessage', err.message))
        },

        async addComment() {
            this.loading = true
            this.axios.post('/posts/' + this.postId + '/comments', {
                comment: this.comment
            }, {
                headers: {
                    'Authorization': 'Bearer ' + this.$store.state.auth.token
                }
            })
            .then(() => {
                this.comment = ''
                this.loading = false
                this.createComment = false
                this.fetchComment()
                    .catch(err => this.$store.commit('displayMessage', err.message))
                this.$router.push({ name: 'Show', params: { id: this.postId }})
            })
            .catch(err => this.$store.commit('displayMessage', err.message))
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
            this.load.comment = true
            this.axios.get('/posts/' + this.postId + '/comments?with=user', {
                headers: {
                    'Authorization': 'Bearer ' + this.$store.state.auth.token
                }
            })
            .then(r => r.data)
            .then(comments => {
                this.comments = comments.data
                this.load.comments = false
            })
            .catch(err => {
                this.load.comments = false
                this.$store.commit('displayMessage', err.message)
            })
        },
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
        }
    }
}
</script>

<style>
.p-show {
    font-size: 16px;
}

.comment-item {
    padding: 16px 0;
}
</style>


