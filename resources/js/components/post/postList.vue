<template>
  <div>
    <v-layout v-for="post in posts" :key="post.id" column class="post">
      <v-layout
        class="post-content"
        @click="$router.push({ name: 'Show', params: { id: post.id } })"
      >
        <v-flex shrink>
          <v-avatar class="mb-3" size="48px">
            <img :src="gravatar(post.user.email)" alt="avatar">
          </v-avatar>
        </v-flex>
        <v-flex>
          <v-layout column>
            <v-layout>
              <h4>{{ post.user.name }}</h4>
              <span color="grey">&nbsp;. {{ getPostTime(post.created_at) }} {{ post.created_at !== post.updated_at ? '(edited)' : null }}</span>
            </v-layout>
            <p class="article mb-0">{{ post.post }}</p>
            <v-layout align-center class="mt-2">
              <v-flex xs12>
                <v-layout align-center>
                  <v-icon class="mr-2">chat_bubble_outline</v-icon>{{ post.comments_count }}
                </v-layout>
              </v-flex>
            </v-layout>
          </v-layout>
        </v-flex>
      </v-layout>
      <v-divider></v-divider>
    </v-layout>
    <app-snackbar></app-snackbar>
  </div>
</template>

<script>
import md5 from 'md5'
import moment from 'moment'
import appSnackbar from '../snackbar'

export default {
    props: {
        posts: {
            type: Array
        }
    },

    components: {
      appSnackbar
    },

    methods: {
        gravatar(email) {
            return 'https://www.gravatar.com/avatar/' + md5(email) + '?d=mp'
        },

        getPostTime(date) {
            moment.locale('id')
            return moment.utc(date).local().fromNow(true)
        },
    }
}
</script>
