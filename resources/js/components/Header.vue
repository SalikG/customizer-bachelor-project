<template>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <router-link class="navbar-brand" to="/">Customizer</router-link>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <!-- use router-link component for navigation. -->
            <!-- specify the link by passing the `to` prop. -->
            <!-- `<router-link>` will be rendered as an `<a>` tag by default -->
            <li class="nav-item"><router-link class="nav-link" to="/">Home</router-link></li>
            <li class="nav-item" v-if="authenticated"><router-link class="nav-link" to="/create-3d-model">New model</router-link></li>
            <li class="nav-item" v-if="authenticated"><router-link class="nav-link" to="/list-3d-models">Your models</router-link></li>
        </ul>
        <ul class="navbar-nav ml-auto mr-3">
            <li class="nav-item float-right" v-if="!authenticated"><router-link class="nav-link" to="/register">Register</router-link></li>
            <li class="nav-item float-right" v-if="!authenticated"><router-link class="nav-link" to="/login">Login</router-link></li>
            <li class="nav-item float-right" v-if="authenticated"><a class="nav-link" id="logout-link" href="#" @click.prevent="signOut">Logout</a></li>
        </ul>
    </div>
</nav>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
    name: "headerComp",
    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
        })
    },
    methods: {
        ...mapActions({
            signOutAction: 'auth/signOut'
        }),
        async signOut () {
            await this.signOutAction();
            await this.$router.push('login');
        }
    }
}
</script>

<style lang="scss" scoped>
nav{
    padding: 0;
    li{
        cursor: pointer;
        padding: 0;
        .nav-link{
            padding: 20px 20px !important;
        }
        .router-link-exact-active{
            background-color: #272727;
        }
    }
    .navbar-brand{
        padding: 10px 30px 10px 30px;
    }
    li:hover{
        background-color: #0f0f11;
    }
}
</style>
