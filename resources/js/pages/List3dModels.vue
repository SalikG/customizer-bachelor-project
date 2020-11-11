<template>
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3" v-for="model in models">
            <div>
                <ModelRenderer ref="modelRenderer" v-bind:background="0xEEEEEE" v-bind:canvas-container-unique-id="model.id" v-bind:model-path="model.file_path"></ModelRenderer>
            </div>
            <h5>{{model.name}}</h5>
        </div>
    </div>
</template>

<script>

import ModelRenderer from '../components/ModelRenderer'

export default {
    name: "List3dModels",
    components: {ModelRenderer},
    data() {
        return {
            models: [
                {
                    id: 0,
                    name: '',
                    file_path: '',
                }
            ]
        }
    },
    mounted() {
        this.get3dModels()
    },
    methods: {
        get3dModels(){
            const self = this;
            axios.get('/get-3d-model-list')
            .then((res) => {
                if (res.status === 200){
                    self.models = res.data;
                    console.log(res.data);
                }
            }).catch((err) => {
                console.log('SERVER ERROR')
            })
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
