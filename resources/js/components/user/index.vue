<template>
    <div>
        <v-toolbar flat fixed color="#fff">
            <v-icon @click="$router.go(-1)">keyboard_backspace</v-icon>
            <v-toolbar-title>
                Profile
            </v-toolbar-title>
        </v-toolbar>
        <v-layout class="content" column>
            <profile-header :user="user"></profile-header>
            <v-divider class="mt-2"></v-divider>
            <v-layout pa-2 align-center justify-center>
                <v-icon medium>list_alt</v-icon>&nbsp;{{ posts.length }} post{{ posts.length > 1 ? 's' : null }}
            </v-layout>
            <v-divider class="mb-2"></v-divider>
            <v-layout column>
                <app-loading></app-loading>
                <post-list :posts="posts" :commentsCount="false"></post-list>
            </v-layout>
            <app-loading v-if="loadPost && page > 1"></app-loading>
        </v-layout>
        <v-btn color="primary" dark medium fixed bottom right fab @click="composer.show = true" >
            <v-icon>add</v-icon>
        </v-btn>
        <app-composer :composer="composer" @close-composer="composer.show = false" @success="fetchPost()"></app-composer>
    </div>
</template>

<script>
    import postList from '../post/postList'
    import appLoading from '../loading'
    import profileHeader from './profileHeader'

    export default {
        components: {
            appLoading,
            postList,
            profileHeader
        },

        data() {
            return {
                user: {},
                posts: [],
                userId: '',
                page: 0,
                lastPage: 1,
                perPage: 15,
                loadUser: false,
                loadPost: false,
                loading: false,
                composer: {
                    type: 'story',
                    action: 'compose',
                    show: false
                }
            }
        },

        computed: {
            thisUser() {
                return this.$store.getters.thisUser(this.$router.currentRoute.params.id)
            }
        },

        methods: {
            getPostTime(date) {
                return this.$store.getters.getTime(date)
            },

            async fetchUser() {
                this.loadUser = true
                if (this.thisUser) {
                    this.user = this.$store.state.auth.user
                }
                else {
                    this.axios.get('/users/' + this.userId, {
                        headers: {
                            'Authorization': 'Bearer ' + this.$store.state.auth.token
                        }
                    })
                        .then(r => r.data)
                        .then(user => {
                            this.loadUser = false
                            this.user = user.data
                        })
                        .catch(err => {
                            this.loadUser = false
                            this.$store.commit('displayMessage', err.message)
                        })
                }
            },

            async fetchPost() {
                this.loadPost = true
                this.$store.commit('getContent', true)
                this.page += 1
                this.axios.get('/users/' + this.userId + '/posts?with=user&per_page=' + this.perPage + '&page=' + this.page, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                    .then(r => r.data)
                    .then(posts => {
                        const temp = this.posts.concat(posts.data)
                        this.posts = temp
                        this.lastPage = posts.meta.last_page
                        this.loadPost = false
                        this.$store.commit('getContent', false)
                    })
                    .catch(err => {
                        this.page -= 1
                        this.loadPost = false
                        this.$store.commit('getContent', false)
                        this.$store.commit('displayMessage', err.message)
                    })
            },

            loadOnScroll() {
                let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                if (bottomOfWindow) {
                    if (this.page <= this.lastPage){
                        this.fetchPost()
                    } else {
                        this.$store.commit('getContent', false)
                        this.endStory = true
                    }
                }
            },
        },

        mounted() {
            window.addEventListener('scroll', this.loadOnScroll)
        },

        created() {
            this.userId = this.$router.currentRoute.params.id
            this.fetchUser()
            this.fetchPost()
        },

        beforeDestroy() {
            window.removeEventListener('scroll', this.loadOnScroll)
        }
    }
</script>


<style>
    .profile-header {
        padding: 16px;
    }

    .profile-header .v-btn {
        margin: 0;
    }
</style>
