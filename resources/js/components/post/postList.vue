<template>
    <div>
        <v-layout v-for="post in posts" :key="post.id" column class="posts">
            <post-item :post="post" :commentsCount="commentsCount"></post-item>
            <v-divider></v-divider>
        </v-layout>
    </div>
</template>

<script>
    import postItem from './postItem'

    export default {
        props: {
            posts: {
                type: Array
            },
            commentsCount: {
                type: Boolean,
                default: true
            }
        },

        components: {
            postItem
        }
    }
</script>
