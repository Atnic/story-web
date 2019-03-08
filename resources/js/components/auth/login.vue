<template>
    <v-layout class="container" align-center justify-center column>
        <v-form>
            <v-layout column>
                <v-flex>
                    <v-text-field
                        v-model="email"
                        :rules="emailRules"
                        label="Email"
                        required
                        :disabled="loading"
                        @keypress.enter="login"
                    ></v-text-field>
                </v-flex>
                <v-flex>
                    <v-text-field
                        v-model="password"
                        type="password"
                        :rules="passwordRules"
                        label="Password"
                        required
                        :disabled="loading"
                        @keypress.enter="login"
                    ></v-text-field>
                </v-flex>
                <v-btn @click="login" :loading="loading">Login</v-btn>
                <router-link v-if="!loading" class="text-xs-center blue--text mt-2" :to="{ name: 'Register' }">Register</router-link>
            </v-layout>
        </v-form>
    </v-layout>
</template>

<script>
    import Cookie from 'js-cookie'
    export default {
        data() {
            return {
                email: '',
                password: '',
                emailRules: [
                    v => !!v || 'E-mail is required',
                    v => /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'E-mail must be valid'
                ],
                passwordRules: [
                    v => !!v || 'Password is required',
                    v => v.length >= 6 || 'Password must be more than 6 characters'
                ],
                loading: false
            }
        },
        methods: {
            async login() {
                this.loading = true
                const v = this.validate()
                if (v) {
                    this.axios.post('/oauth/token', {
                        grant_type: 'password',
                        client_id: process.env.MIX_VUE_APP_CLIENT_ID,
                        client_secret: process.env.MIX_VUE_APP_CLIENT_SECRET,
                        username: this.email,
                        password: this.password
                    })
                        .then(r => r.data)
                        .then(user => {
                            const token = user.access_token
                            this.axios.get('/user', {
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                }
                            })
                                .then(r => r.data)
                                .then(user => {
                                    Cookie.set('_user', user)
                                    Cookie.set('_tkn', token)
                                    this.$store.state.auth.token = user.token
                                    this.$store.state.auth.user = user
                                    this.loading = false
                                    this.$router.replace({ name: 'Home' })
                                })
                        })
                        .catch(err => {
                            let status = err.message.split(' ')
                            status = status[status.length-1]
                            Cookie.remove('_tkn')
                            Cookie.remove('_user')
                            this.loading = false
                            if (status == '401') this.$store.commit('displayMessage', 'Wrong email & password combination (401)')
                            else this.$store.commit('displayMessage', err.message)
                        })
                } else {
                    this.loading = false
                    this.$store.commit('displayMessage', 'Invalid Input')
                }
            },
            validate() {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                let email = re.test(String(this.email).toLowerCase())
                let password = this.password.length > 0
                return email && password
            }
        }
    }
</script>

<style scoped>
    .container {
        padding: 32px;
    }
    form.v-form {
        width: 100%;
    }
    .v-btn {
        margin: 8px 0;
    }
</style>
