require('./bootstrap');
window.THREE  = require('three')
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
window.Renderer = new THREE.WebGLRenderer();

window.Scene = new THREE.Scene();
window.Camera = new THREE.PerspectiveCamera( 45, window.innerWidth / window.innerHeight, 1, 10000 );
window.OrbitControls = new OrbitControls( window.Camera, window.Renderer.domElement );

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

//Components
import Index from './pages/Index'
import Create3dModel from './pages/Create3dModel'

//Routes
const routes = [
    { path: '/', component: Index },
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
