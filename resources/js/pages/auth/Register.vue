<template>
    <div class="row justify-content-md-center">
        <div class="col-12 col-sm-12 col-md-8 col-lg-6">
            <h1>Register</h1>
            <form v-on:submit.prevent="checkRegisterForm" method="post">
                <div class="form-group">
                    <label for="companyName">Company name</label>
                    <input v-model="RegisterData.companyName" type="text" class="form-control" id="companyName">
                    <p v-if="'companyNameError' in errors" class="errorMessage">{{ this.errors.companyNameError }}</p>
                </div>
                <div class="row">
                    <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                        <label for="firstname">Firstname</label>
                        <input v-model="RegisterData.firstname" type="text" class="form-control" id="firstname">
                    </div>
                    <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                        <label for="lastname">Lastname</label>
                        <input v-model="RegisterData.lastname" type="text" class="form-control" id="lastname">
                    </div>
                    <p v-if="'nameError' in errors" class="errorMessage px-3">{{ this.errors.nameError }}</p>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input v-model="RegisterData.email" type="email" class="form-control" id="email" aria-describedby="emailHelp">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    <p v-if="'emailError' in errors" class="errorMessage">{{ this.errors.emailError }}</p>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input v-model="RegisterData.password" type="password" class="form-control" id="password">
                    <p v-if="'passwordError' in errors" class="errorMessage">{{ this.errors.passwordError }}</p>
                </div>
                <p v-for="error in serverResponseErrors" class="errorMessage pb-3">{{ error }}</p>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "Register",
    data() {
        return {
            // error format => {"inputID": "errorMessage"}
            serverResponseErrors: [],
            errors: {},
            RegisterData: {
                companyName: 'TestCompany',
                firstname: 'TestFirstname',
                lastname: 'TestLastname',
                email: 'test@test.dk',
                password: '1234Qwer',
                // companyName: '',
                // firstname: '',
                // lastname: '',
                // email: '',
                // password: '',
            }
        }
    },
    methods: {
        checkRegisterForm: function () {
            this.errors = {};
            this.serverResponseErrors = [];

            if (!this.RegisterData.companyName) {
                this.errors.companyNameError = 'Company name required.';
            }
            if (!this.RegisterData.firstname || !this.RegisterData.lastname) {
                this.errors.nameError = 'Full name required.';
            }
            if (!this.RegisterData.email) {
                this.errors.emailError = 'Email required.';
            }
            if(!RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*?[0-9]){8,}").test(this.RegisterData.password)){
                this.errors.passwordError = 'Minimum eight characters, at least one uppercase letter, one lowercase letter and one number.';
            }

            if (_.isEmpty(this.errors)){
                this.submitForm()
            }
        },

        submitForm(){
            const self = this;
            axios.post('/auth/register',
                this.RegisterData,
            {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((res) =>{
                    if(res.status === 200){
                        self.$router.push('login')
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422){
                        for(const errFieldName in error.response.data.errors ){
                            for(const errorMsgIndex in error.response.data.errors[errFieldName]){
                                this.serverResponseErrors.push(error.response.data.errors[errFieldName][errorMsgIndex]);
                            }
                        }
                    }
                    else if (error.response.status === 500){
                        this.serverResponseErrors.push('SERVER ERROR');
                    }
                })
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
