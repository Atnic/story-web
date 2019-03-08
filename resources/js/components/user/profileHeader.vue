<template>
    <v-layout align-center class="user-info profile-header">
        <v-avatar size="72px" class="mr-3">
            <img :src="user.email ? gravatar(user.email) : gravatar('undefined')" alt="avatar">
        </v-avatar>
        <v-layout column>
            <h3>{{ user.name }}</h3>
            <span class="blue-grey--text text--darken-2">{{ user.email }}</span>
        </v-layout>
    </v-layout>
</template>

<script>
    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },

        methods: {
            gravatar(email) {
                return this.$store.getters.gravatar(email)
            }
        }
    }
</script>
