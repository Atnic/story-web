import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

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
        drawer: false,
        snackbar: {
            show: false,
            message: '',
            success: false
        },
        loadContent: false,
        loadPostContent: false
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
        }
    },

    actions: {
    }
})