import Vue from 'vue'
import Vuex from 'vuex'
import moment from 'moment'
import md5 from 'md5'
import marked from 'marked'

Vue.use(Vuex)

moment.locale('id')

export const store = new Vuex.Store({
    state: {
        auth: {
            token: '',
            user: {
                id: '',
                name: '',
                email: ''
            }
        },
        snackbar: {
            show: false,
            message: '',
            success: false
        },
        loadContent: false,
        editContent: '',
        giphyURL: ''
    },

    getters: {
        thisUser: state => userID => {
            return state.auth.user.id == userID
        },

        getTime: state => date => {
            return moment.utc(date).local().fromNow(true)
        },

        gravatar: state => email => {
            return 'https://www.gravatar.com/avatar/' + md5(email) + '?d=mp'
        },

        markedText: state => text => {
            return marked(text, { sanitize: true })
        }
    },

    mutations: {
        displayMessage(state, payload) {
            state.snackbar = {
                show: true,
                message: payload.message ? payload.message : payload,
                success: payload.success ? payload.success : false
            }
        },

        getContent(state, payload) {
            state.loadContent = payload
        },

        postLoading(state, payload) {
            state.compose.loading = payload
        }
    },

    actions: {
    }
})
