<template>
    <v-layout
        class="post-content"
        @click="$router.push({ name: 'Show', params: { id: post.id } })"
    >
        <v-flex shrink>
            <v-avatar class="mb" size="41px">
                <img :src="gravatar(post.user.email)" alt="avatar">
            </v-avatar>
        </v-flex>
        <v-flex>
            <v-layout column class="pl-1 pr-1">
                <v-layout wrap>
                    <h4>{{ post.user.name }}</h4>
                    <span color="grey">&nbsp;. {{ getPostTime(post.created_at) }} {{ post.created_at !== post.updated_at ? '(edited)' : null }}</span>
                </v-layout>
                <div class="item-content mt-0 mb-0 markdown-body" style="word-break: break-word;" v-html="$store.getters.markedText(text)"></div>
                <!-- <div v-if="isGiphy" class="giphy-container ma-0">
                    <img :src="giphyURL" style="width: 100%; border-radius: 4px; display: block;">
                </div> -->
                <v-layout v-if="commentsCount" align-center class="mt-2">
                    <v-flex xs12>
                        <v-layout align-center>
                            <v-icon class="mr-2">chat_bubble_outline</v-icon>{{ post.comments_count }}
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        props: {
            post: {
                type: Object,
                required: true
            },
            commentsCount: {
                type: Boolean,
                default: true
            }
        },

        data() {
            return {
                giphyURL: '',
                text: ''
            }
        },

        computed: {
            isGiphy() {
                return this.post.post.match(/^\/giphy\shttps:\/\/.+\.gif\s+.+/);
            }
        },

        methods: {
            gravatar(email) {
                return this.$store.getters.gravatar(email)
            },

            getPostTime(date) {
                return this.$store.getters.getTime(date)
            }
        },

        created() {
            // if (this.isGiphy) {
            //     let temp = this.post.post.split(' ')
            //     temp.splice(0, 2)
            //     this.text = '<span class="grey--text">' + this.post.post.split(' ')[0] + '</span> ' + temp.join(' ')
            //     this.giphyURL = this.post.post.split(' ')[1]
            // } else {
            this.text = this.post.post
            // }
        }
    }
</script>

<style scoped>
    .giphy {
        margin-top: 4px;
        background-color: #eee;
        border-radius: 8px;
        overflow: hidden;
    }

    .giphy img {
        width: 100%;
        display: block;
    }

    .post-content {
        padding: 12px 5px 12px 5px;
    }

    .post-content > div {
        margin: 0 5px;
    }

    .giphy-container {
        min-height: 160px;
    }
</style>

