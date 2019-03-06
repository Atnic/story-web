import VueRouter from 'vue-router'
import Cookie from 'js-cookie'
import { store } from '../store/store'
import routes from './routes'

const router = new VueRouter({ mode: 'history', routes })

router.beforeEach((to, from, next) => {
    let tkn = Cookie.get('_tkn')
    let user = Cookie.get('_user')
    if (to.matched.some(r => r.meta.public)) {
        if (tkn && user) {
            store.state.auth.token = tkn
            store.state.auth.user = JSON.parse(user)
            next({ name: 'Home' })
        } else {
            next()
        }
    } else {
        if(to.matched.some(r => r.meta.redirect)) {
            if (tkn && user) {
                store.state.auth.token = tkn
                store.state.auth.user = JSON.parse(user)
                next({ name: 'Home' })
            } else {
                next({ name: 'Login' })
            }
        } else {
            if (tkn && user) {
                store.state.auth.token = tkn
                store.state.auth.user = JSON.parse(user)
                next()
            } else {
                next({ name: 'Login' })
            }
        }
        next()
    }
    next()
})

export default router