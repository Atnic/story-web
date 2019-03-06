<template>
    <div>
        <v-list class="pa">
            <v-list-tile avatar>
                <v-list-tile-avatar>
                    <img :src="$store.state.auth.user.email ? gravatar($store.state.auth.user.email) : gravatar('undefined')" alt="avatar">
                </v-list-tile-avatar>

                <v-list-tile-content>
                <v-list-tile-title>{{ $store.state.auth.user.name }}</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
            </v-list>

            <v-list class="menu-list" dense>
            <v-divider></v-divider>
            
            <router-link :to="{ name: 'User', params: { id: $store.state.auth.user.id }}">
            <v-list-tile>
                <v-list-tile-action>
                    <v-icon medium>person_outline</v-icon>
                </v-list-tile-action>
                <v-list-tile-content>
                    <v-list-tile-title>Profile</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
            </router-link>
            
            <v-divider></v-divider>
            <v-list-tile @click="logout">
                <v-list-tile-action>
                    <v-icon color="red" medium>exit_to_app</v-icon>
                </v-list-tile-action>
                <v-list-tile-content>
                    <v-list-tile-title>Logout</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
        </v-list>
    </div>
</template>

<script>
import Cookie from 'js-cookie'
import md5 from 'md5'

export default {
    methods: {
        logout() {
            try {
                Cookie.remove('_tkn')
                this.$store.state.auth.token = ''
                this.$router.push({ name: 'Login' })
            } catch (err) {
                this.$store.commit('displayMessage', err.message)
            }
        },
        
        gravatar(email) {
            return 'https://www.gravatar.com/avatar/' + md5(email) + '?d=mp'
        }
    }
}
</script>
