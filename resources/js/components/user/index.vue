<template>
    <div>
        <v-toolbar flat fixed>
            <v-icon @click="$router.go(-1)">keyboard_backspace</v-icon>
            <v-toolbar-title>
                Profile
            </v-toolbar-title>
        </v-toolbar>
        <v-layout align-center class="user-info content profile-header">
            <v-avatar size="72px" class="mr-3">
                <img :src="user.email ? gravatar(user.email) : gravatar('undefined')" alt="avatar">
            </v-avatar>
            <v-layout column>
                <h3>{{ user.name }}</h3>
                <span class="blue-grey--text text--darken-2">{{ user.email }}</span>
            </v-layout>
        </v-layout>
        <v-divider class="mt-2"></v-divider>
        <v-layout pa-2 align-center justify-center>
            <v-icon medium>list_alt</v-icon>&nbsp;{{ posts.length }} post{{ posts.length > 1 ? 's' : null }}
        </v-layout>
        <v-divider class="mb-2"></v-divider>
        <v-layout v-if="!$store.state.loadContent" column>
            <v-layout column v-for="post in posts" :key="post.id">
                <v-layout @click="$router.push({ name: 'Show', params: { id: post.id } })" class="post-content post">
                    <v-flex shrink>
                        <v-avatar size="48px">
                            <img :src="user.email ? gravatar(user.email) : gravatar('undefined')" alt="avatar">
                        </v-avatar>
                    </v-flex>
                    <v-flex>
                        <v-layout>
                            <h4>{{ user.name }}</h4>
                            <span color="grey">&nbsp;. <span>{{ getPostTime(post.created_at) }} {{ post.created_at !== post.updated_at ? '(edited)' : null }}</span></span>
                        </v-layout>
                        <p class="article mb-0">{{ post.post }}</p> 
                    </v-flex>
                </v-layout>
            </v-layout>
        </v-layout>
        <v-dialog
            class="dialog"
            v-model="compose"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            >
            <v-toolbar flat>
                <v-icon @click="loading ? null : compose = false">close</v-icon>
                <v-toolbar-title>Compose story</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn v-if="post" :loading="loading" @click="createPost">post</v-btn>
            </v-toolbar>
            <v-layout class="post-input">
                <v-textarea label="what's happening?" rows="3" v-model="post" auto-grow :disabled="loading"></v-textarea>
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
            v-show="!compose"
            @click="compose = true"
            v-if="thisUser"
        >
            <v-icon>add</v-icon>
        </v-btn>
        <app-loading></app-loading>
    </div>
</template>

<script>
import appLoading from '../loading'
import md5 from 'md5'
import moment from 'moment'

export default {
    components: {
        appLoading
    },
    data() {
        return {
            user: {},
            posts: [],
            compose: false,
            post: '',
            loading: false
        }
    },

    computed: {
        thisUser() {
            return this.$store.state.auth.user.id == this.$router.currentRoute.params.id
        }
    },

    methods: {
        getPostTime(date) {
            moment.locale('id')
            return moment.utc(date).local().fromNow(true)
        },

        async fetchData() {
            this.$store.state.loadContent = true
            const userId = this.$router.currentRoute.params.id
            this.axios.get('/users/' + userId, {
                headers: {
                    'Authorization': 'Bearer ' + this.$store.state.auth.token
                }
            })
                .then(r => r.data)
                .then(user => {
                    this.user = user.data
                })

            this.axios.get('/users/' + userId + '/posts', {
                headers: {
                    'Authorization': 'Bearer ' + this.$store.state.auth.token
                }
            })
                .then(r => r.data)
                .then(posts => {
                    this.posts = posts.data
                    this.$store.state.loadContent = false
                })
        },

        createPost() {
            this.loading = true;
            this.axios
                .post('/posts', {
                    post: this.post
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + this.$store.state.auth.token
                    }
                })
                .then(() => {
                    this.loading = false;
                    this.posts = []
                    this.fetchData();
                    this.compose = false;
                    this.post = ''
                    this.$router.push({ name: 'User', params: { id: $store.state.auth.user.id }})
                })
                .catch(err => {
                    this.$store.commit('getContent', false)
                })
        },

        gravatar(email) {
            return 'https://www.gravatar.com/avatar/' + md5(email) + '?d=mp'
        }
    },

    created() {
        this.fetchData()
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

