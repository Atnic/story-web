<template>
    <div>
        <v-toolbar flat>
            <v-icon @click="compose = false">close</v-icon>
            <v-toolbar-title>
                Compose Post
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn v-if="post">post</v-btn>
        </v-toolbar>
        <v-layout class="post-input">
            <v-textarea
                label="tulis post anda"
                rows="3"
                v-model="post"
                auto-grow>
            </v-textarea>
        </v-layout>
    </div>
</template>

<script>
export default {
    props: {
        compose: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            post: false
        }
    }
}
</script>

<style>
    .post-input {
        padding: 16px;
    }
</style>


