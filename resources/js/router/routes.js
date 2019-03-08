import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../components/auth/login.vue'
import Home from '../components/home/index.vue'
import Show from '../components/post/show.vue'
import User from '../components/user/index.vue'
import EditUser from '../components/user/edit.vue'
import Register from '../components/auth/register.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'Login',
        meta: {
            public: true
        },
        component: Login
    },
    {
        path: '/register',
        name: 'Register',
        meta: {
            public: true
        },
        component: Register
    },
    {
        path: '/home',
        name: 'Home',
        component: Home,
        meta: {
            keepAlive: true
        }
    },
    {
        path: '/p/:id',
        name: 'Show',
        component: Show
    },
    {
        path: '/user/:id',
        name: 'User',
        component: User
    },
    {
        path: '/user/:id/edit',
        name: 'Edit User',
        component: EditUser
    },
    {
        path: '*',
        name: 'No Page',
        meta: {
            redirect: true
        }
    }
]

export default routes
