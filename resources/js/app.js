require('./bootstrap');
window.THREE  = require('three')

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

//Components
import Index from './pages/Index'
import Login from './pages/auth/Login'
import Register from './pages/auth/Register'
import Create3dModel from './pages/Create3dModel'

//Routes
const routes = [
    { path: '/', component: Index },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/create-3d-model', component: Create3dModel }
]

const router = new VueRouter({
    routes // short for `routes: routes`
})

const app = new Vue({
    el: '#app',
    components: {Index},
    router
}).$mount('#app');
