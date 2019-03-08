<template>
    <div class="ma-0">
        <v-layout v-for="comment in comments" :key="comment.id + comment.comment" column>
            <comment-item :comment="comment" @update-comment="updateComment()" @item-update="update($event)"></comment-item>
            <v-divider></v-divider>
        </v-layout>
    </div>
</template>

<script>
    import commentItem from './commentItem'
    export default {
        data() {
            return {
                loadingDelete: false,
                deleteDialog: false,
                selectedCommentId: ''
            }
        },

        components: {
            commentItem
        },

        props: {
            comments: {
                type: Array,
                required: true
            },
        },

        methods: {
            updateComment() {
                this.$emit('success')
            },

            update(e) {
                this.$emit('update-comment-item', e)
            }
        }
    }
</script>

<style>
    .comment-item {
        padding: 16px 5px;
    }

    .comment-item p {
        margin-bottom: 0;
    }
</style>

