<template>
    <v-layout class="comment-item">
        <v-flex shrink @click="$router.push({ name: 'User', params: { id: comment.user_id } })">
            <v-avatar size="41px" class="mr-2">
                <img :src="comment.user.email ? gravatar(comment.user.email) : gravatar('undefined')" alt="avatar">
            </v-avatar>
        </v-flex>
        <v-flex>
            <v-layout column>
                <v-layout>
                    <v-flex>
                        <v-layout wrap>
                            <h4 @click="$router.push({ name: 'User', params: { id: comment.user_id } })">{{ comment.user.name }}</h4>
                            <span>&nbsp;. {{ getPostTime(comment.created_at) }} {{ comment.created_at !== comment.updated_at ? '(edited)' : null }}</span>
                        </v-layout>
                        <div class="item-content" style="word-break: break-word;" v-html="$store.getters.markedText(text)" ></div class="item">
                    </v-flex>
                    <v-flex shrink v-if="comment.user.id == $store.state.auth.user.id">
                        <v-menu>
                            <v-icon
                                slot="activator"
                                flat
                                ripple
                                medium>more_vert
                            </v-icon>
                            <v-list>
                                <v-list-tile @click="editComment(comment.id, comment.comment)" >
                                    <v-list-tile-title>Edit</v-list-tile-title>
                                </v-list-tile>
                                <v-divider></v-divider>
                                <v-list-tile @click="deleteDialog = true">
                                    <v-list-tile-title>
                                        <v-menu>
                                            <span slot="activator" class="red--text" flat>Delete</span>
                                            <v-dialog v-model="deleteDialog" persistent max-width="290">
                                                <v-card v-if="!loadingDelete">
                                                    <v-card-title class="subheading">Delete your comment?</v-card-title>
                                                    <v-card-actions>
                                                        <v-spacer></v-spacer>
                                                        <v-btn flat @click="deleteDialog = false">No</v-btn>
                                                        <v-btn color="red" flat @click="deleteComment(comment.post_id, comment.id)">Sure</v-btn>
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
                                        </v-menu>
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </v-flex>
                </v-layout>
                <!-- <div v-if="isGiphy" class="giphy-container" style="margin-right: 8px; margin-top: 6px;">
                    <img :src="giphyURL" style="width: 100%; border-radius: 4px;">
                </div> -->
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        props: {
            comment: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                giphyURL: '',
                text: '',
                deleteDialog: false,
                loadingDelete: false
            }
        },

        computed: {
            isGiphy() {
                return this.comment.comment.match(/^\/giphy\shttps:\/\/.+\.gif\s+.+/);
            }
        },

        methods: {
            getPostTime(date) {
                return this.$store.getters.getTime(date)
            },

            gravatar(email) {
                return this.$store.getters.gravatar(email)
            },

            editComment(commentId, body) {
                this.$store.state.editContent = body
                this.$emit('item-update', {body, id: commentId})
            },

            deleteComment(postId, commentId) {
                this.loadingDelete = true
                this.axios.delete(`/posts/${postId}/comments/${commentId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(() => {
                        this.loadingDelete = false
                        this.deleteDialog = false
                        this.$emit('update-comment')
                    })
                    .catch(err => {
                        this.loadingDelete = false
                        this.deleteDialog = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },
        },

        created() {
            // if (this.isGiphy) {
            //     let temp = this.comment.comment.split(' ')
            //     temp.splice(0, 2)
            //     this.text = '<span class="grey--text">' + this.comment.comment.split(' ')[0] + '</span> ' + temp.join(' ')
            //     this.giphyURL = this.comment.comment.split(' ')[1]
            // } else {
            this.text = this.comment.comment
            // }
        }
    }
</script>

<style scoped>
    .giphy-container > img {
        display: block;
        background-color: #eee;
    }
</style>
