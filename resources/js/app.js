require('./bootstrap');
window.THREE  = require('three')

import Vue from 'vue'
import Vuex, {mapGetters} from 'vuex'
import VueRouter from 'vue-router'
import axios from 'axios'

Vue.use(Vuex)
Vue.use(VueRouter)

//Axios Config
axios.defaults.withCredentials = true
axios.defaults.baseURL = 'http://127.0.0.1:8000'

//Components
import Header from './components/Header'
import Index from './pages/Index'
import Login from './pages/auth/Login'
import Register from './pages/auth/Register'
import Create3dModel from './pages/Create3dModel'
import List3dModels from './pages/List3dModels'

// Stores
import auth from './stores/auth'

const store = new Vuex.Store({
    modules: {
        auth
    }
})


//Routes
const routes = [
    { path: '/', component: Index, meta: {allowAnonymous: true} },
    { path: '/login', component: Login, name: 'login', meta: {allowAnonymous: true} },
    { path: '/register', component: Register, meta: {allowAnonymous: true} },
    { path: '/create-3d-model', component: Create3dModel, name: 'create-3d-model'},
    { path: '/list-3d-models', component: List3dModels, name: 'list-3d-models'}
]

const router = new VueRouter({
    routes, // short for `routes: routes`
})

//Route GUARD
router.beforeEach((to, from, next) => {
    if (to.name === 'login' && store.getters['auth/authenticated']) {
        next({ path: '/' })
    }
    else if (!to.meta.allowAnonymous && !store.getters['auth/authenticated']) {
        next({
            path: '/login',
            query: { redirect: to.fullPath }
        })
    }
    else {
        next()
    }
})



//The dispatch ensures that a refresh does not make the client loose authentication
const app = store.dispatch('auth/me').then(() => {
        new Vue({
        el: '#app',
        components: {Header},
        data(){
            return {
                isLoggedIn: localStorage.getItem('isLoggedIn'),
            }
        },
        template:
            "<div class='container'>" +
                "<Header></Header> " +
                "<router-view :key='$route.fullPath'></router-view>" +
            "</div>",
        router,
        store: store
    })
});
