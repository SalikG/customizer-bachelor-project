<template>
    <div class="row justify-content-center">
        <div class="col-6 col-sm-6 col-md-5 col-lg-5">
            <h1>Login</h1>
            <form v-on:submit.prevent="checkLoginForm" method="post">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input v-model="LoginData.email" type="email" class="form-control" id="email" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input v-model="LoginData.password" type="password" class="form-control" id="password">
                </div>
                <p v-for="error in serverResponseErrors" class="errorMessage pb-3">{{ error }}</p>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "Login",
    data(){
        return {
            serverResponseErrors: [],
            LoginData: {
                email: 'test@test.dk',
                password: '1234Qwer'
            }
        }
    },

    methods: {
        ...mapActions({
            signIn: 'auth/signIn'
       }),
        checkLoginForm(){
            this.serverResponseErrors = [];

            if (!this.LoginData.email){
                this.errors.emailError = 'Email required.';
            }
            if(!this.LoginData.password){
                this.errors.passwordError = 'Password required';
            }

            if (_.isEmpty(this.errors)){
                this.submitForm()
            }
        },

        async submitForm(){
            let form = new FormData();
            form.append('email', this.LoginData.email);
            form.append('password', this.LoginData.password);
            let res = await this.signIn(form)
            if (res === false){this.serverResponseErrors.push('Wrong credentials'); return}
            await this.$router.push('list-3d-models')
        }
    }
}
</script>

<style scoped>
    .errorMessage{
        width: 100%;
        margin-top: .25rem;
        font-size: 85%;
        color: #dc3545;
        margin-bottom: 0;
    }
</style>
