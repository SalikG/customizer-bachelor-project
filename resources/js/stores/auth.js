import axios from 'axios'

export default {
    namespaced: true,

    state: {
        authenticated: false,
        user: null
    },

    getters: {
        authenticated (state) {
            return state.authenticated
        },

        user (state) {
            return state.user
        },
    },

    mutations: {
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },

        SET_USER (state, value) {
            state.user = value
        }
    },

    actions: {
        async signIn ({ dispatch }, credentials) {
            await axios.get('/sanctum/csrf-cookie')
            let success = await axios.post('auth/login', credentials)
                .then((res) => {
                    if (res.status === 200){
                        return true;
                    }
                })
                .catch((err)=>{
                return false;
            });
            if (success === false){return false}
            return dispatch('me')
        },

        async signOut ({ dispatch }) {
            await axios.post('auth/logout')
            return dispatch('me')
        },

        me ({ commit }) {
            return axios.get('/api/user').then((response) => {
                commit('SET_AUTHENTICATED', true)
                commit('SET_USER', response.data)
            }).catch(() => {
                commit('SET_AUTHENTICATED', false)
                commit('SET_USER', null)
            })
        }
    }
}
