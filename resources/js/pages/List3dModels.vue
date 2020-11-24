<template>
    <div>
        <router-link to="/create-3d-model" >
            <button class="btn btn-secondary mb-2">New model</button>
        </router-link>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 mb-3 model-list-item" v-for="model in models">
                <router-link :to="{name: 'view-3d-model', params: {model: model}}">
                    <div>
                        <div class="model-image" v-bind:style="{'background': 'linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.9)),url(' + model.display_img_path + ')'}">
                        </div>
                        <p class="dot-text text-light model-name">
                            {{model.name}}
                        </p>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>


export default {
    name: "List3dModels",
    data() {
        return {
            models: []
        }
    },
    mounted: function() {
        this.get3dModels()
    },
    methods: {
        get3dModels(){
            const self = this;
            axios.get('/models')
            .then((res) => {
                if (res.status === 200){
                    self.models = res.data;
                }
            }).catch((err) => {
                console.log('SERVER ERROR')
            })
        }
    }
}
</script>

<style lang="scss" scoped>
    .dot-text{
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1; /* after 3 line show ... */
        -webkit-box-orient: vertical;
    }
    .model-list-item {
        cursor: pointer;
        height: 200px;
        width: 200px;
        div{
            height: 100%;
            width: 100%;
            overflow: hidden;

            .model-image {
                height: 100%;
                width: 100%;
                background-size: contain !important;
                background-repeat: no-repeat !important;
                background-position: center !important;
                transition: transform 0.2s;
            }
            .model-name {
                position: absolute;
                bottom: 0;
                left: 30px;
                font-size: 1.2rem;
                text-decoration: underline;
            }
        }
    }
    .model-list-item:hover {
        .model-image{
            transform: scale(1.05);
        }
    }
</style>
