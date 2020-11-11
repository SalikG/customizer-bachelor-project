<template>
<nav>
    <ul>
        <!-- use router-link component for navigation. -->
        <!-- specify the link by passing the `to` prop. -->
        <!-- `<router-link>` will be rendered as an `<a>` tag by default -->
        <router-link to="/">Go to Index</router-link>
        <router-link to="/create-3d-model">Go to 3d model creation</router-link>
        <router-link to="/list-3d-models">Your models</router-link>
        <router-link to="/register">Register</router-link>
        <router-link to="/login">Login</router-link>
        <li v-if="authenticated">
            <a id="logout-link" href="#" @click.prevent="signOut">Logout</a>
        </li>
    </ul>
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

<style scoped>

</style>
