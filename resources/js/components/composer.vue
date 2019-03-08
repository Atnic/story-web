<template>
    <v-dialog v-model="composer.show" fullscreen hide-overlay transition="dialog-bottom-transition">
        <v-toolbar flat>
            <v-icon @click="close()" :disabled="loading">close</v-icon>
            <v-toolbar-title>{{ composer.action + ' ' + composer.type }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn round color="primary" v-if="canDoAction" :loading="loading" @click="isEditGiphy || (isGiphy && giphyURL == 404) ? getGiphyURL() : doAction()">{{ isEditGiphy || (isGiphy && giphyURL == 404) ? 'search' : composer.action == 'edit' ? 'edit' : 'post'}}</v-btn>
        </v-toolbar>
        <v-layout class="post-input pb-0">
            <v-textarea
                label="what's happening?"
                rows="3"
                v-model="text"
                auto-grow
                :disabled="loading"
                :class="isGiphy ? 'is-giphy' : ''"></v-textarea>
        </v-layout>
        <v-layout column v-if="isGiphy" class="pl-3 pr-3" align-center>
            <v-btn class="shuffle-giphy primary ma-0 mb-4" @click="getGiphyURL()" :loading="loadGiphy" :disabled="loading">{{ (giphyURL && giphyURL !== 404) ? 'Shuffle' : 'Search' }}</v-btn>
            <img v-if="giphyURL !== 404" class="show-giphy" :src="giphyURL">
            <div v-if="giphyURL == 404" class="text-xs-center"><h2>Sorry :(</h2>Giphy can't get any gif from this tag,<br/>please try another tag.</div>
        </v-layout>
        <v-dialog v-model="dialog" persistent max-width="290">
            <v-card>
                <v-card-title class="headline">Discard?</v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" flat @click="discard()">Yes</v-btn>
                    <v-btn color="primary" flat @click="dialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-dialog>
</template>

<script>
    export default {
        props: {
            composer: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                dialog: false,
                text: this.$store.state.editContent,
                giphyURL: this.$store.state.giphyURL,
                loadGiphy: false,
                temp: {
                    text: this.$store.state.editContent,
                    giphyURL: this.$store.state.giphyURL,
                }
            }
        },

        computed: {
            canDoAction() {
                if (this.composer.action == 'compose') return this.text.trim()
                else {
                    if (this.isGiphy) {
                        if (this.text !== this.temp.text && this.giphyURL !== this.temp.giphyURL) return true
                        else if (this.text !== this.temp.text && this.giphyURL == this.temp.giphyURL) return true
                        else if (this.isGiphy && this.text == this.temp.text && this.giphyURL !== this.temp.giphyURL) return true
                        else return false
                    }
                    return this.text.trim() && this.text.trim() !== this.$store.state.editContent.trim()
                }
            },

            isGiphy() {
                return false
                return this.text.match(/^\/giphy\s+.+/g) && this.text.trim().split(' ').length > 1
            },

            isEditGiphy() {
                return false
                // if (this.isGiphy && this.text !== this.temp.text && this.giphyURL !== this.temp.giphyURL) return true
                // else if (this.isGiphy && this.text !== this.temp.text && this.giphyURL == this.temp.giphyURL) return true
                // else if (this.isGiphy && this.text == this.temp.text && this.giphyURL !== this.temp.giphyURL) return false
            }
        },

        methods: {
            reset() {
                this.text = ''
                this.giphyURL = ''
                this.temp.text = ''
                this.temp.giphyURL = ''
                this.$store.state.editContent = ''
                this.$store.state.giphyURL = ''
            },

            doAction() {
                if (this.composer.action == 'compose') {
                    if (this.composer.type == 'story') this.createPost();
                    else this.createComment();
                } else {
                    if (this.composer.type == 'story') this.editPost();
                    else this.editComment();
                }
            },

            async getGiphyURL () {
                this.temp.text = this.text
                let tag = this.text.split(' ')
                tag.shift()
                tag.join(' ')
                this.loadGiphy = true
                await this.axios.get(`https://api.giphy.com/v1/gifs/random?api_key=${process.env.VUE_APP_GIPHY_KEY}&tag=${tag}&rating=G`)
                    .then(r => {
                        if (!r.data.data.image_url) {
                            this.loadGiphy = false
                            this.giphyURL = 404
                        } else {
                            this.loadGiphy = false
                            this.temp.giphyURL = this.giphyURL ? this.giphyURL : r.data.data.image_url
                            this.giphyURL = r.data.data.image_url
                        }
                    })
                    .catch(err => {
                        this.loadGiphy = false
                        this.$store.commit('displayMessage', err.message)
                    })

            },

            giphyFormat() {
                let post = this.text.split(' ')
                post.splice(1, 0, this.giphyURL)
                post = post.join(' ')
                return post
            },

            async createPost() {
                let post = this.giphyFormat()
                this.loading = true;
                this.axios
                    .post('/posts', {
                        post: this.isGiphy ? post : this.text
                    }, {
                        headers: {
                            'Authorization': 'Bearer ' + this.$store.state.auth.token
                        }
                    })
                    .then(() => {
                        this.loading = false
                        this.reset()
                        this.$emit('success')
                        this.$emit('close-composer')
                    })
                    .catch(err => {
                        this.loading = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            async createComment() {
                let comment = this.giphyFormat()
                this.loading = true
                this.axios.post('/posts/' + this.composer.postId + '/comments', {
                    comment: this.isGiphy ? comment : this.text
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(() => {
                        this.loading = false
                        this.reset()
                        this.$emit('success')
                        this.$emit('close-composer')
                    })
                    .catch(err => {
                        this.loading = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            async editPost() {
                let post = this.giphyFormat()
                if (this.text.trim() && this.text.trim() !== this.$store.state.currentEditContent) {
                    this.loading = true
                    this.axios.put('/posts/' + this.composer.postId, {
                        post: this.isGiphy ? post : this.text
                    }, {
                        headers: {
                            'Authorization': 'Bearer ' + this.$store.state.auth.token
                        }
                    })
                        .then(() => {
                            this.loading = false
                            this.reset()
                            this.$emit('success', 'edit post')
                            this.$emit('close-composer')
                        })
                        .catch(err => {
                            this.loading = false
                            this.$store.commit('displayMessage', err.message)
                        })
                }
            },

            async editComment() {
                let comment = this.giphyFormat()
                this.loading = true
                this.axios.put(`/posts/${this.composer.postId}/comments/${this.composer.commentId}`, {
                    comment: this.isGiphy ? comment : this.text
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(() => {
                        this.loading = false
                        this.reset()
                        this.$emit('success')
                        this.$emit('close-composer')
                    })
                    .catch(err => {
                        this.loading = false
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            close() {
                if (this.canDoAction) {
                    this.dialog = true
                } else {
                    this.discard()
                }
            },

            discard() {
                this.loading = false
                this.text = ''
                this.$store.state.editContent = ''
                this.$emit('close-composer')
            }
        },
    }
</script>

<style>
    .show-giphy {
        width: 100%;
        max-width: 400px;
        border-radius: 4px;
        margin: 0 auto;
    }

    .shuffle-giphy {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }

    .is-giphy textarea {
        color: #828282 !important;
    }
</style>

