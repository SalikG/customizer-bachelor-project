<template>
    <div class="row">
        <div id="leftPanel" class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
            <label>File
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
            </label>
            <button v-on:click="loadFile()">Load</button>
        </div>
        <div id="rightPanel" class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
            <ModelRenderer v-bind:model-path="currentFilePath"></ModelRenderer>
        </div>
    </div>
</template>

<script>

    import ModelRenderer from '../components/ModelRenderer'

    export default {
        name: "Create3dModel",
        components: {ModelRenderer},
        data() {
            return {
                file: '',
                currentFilePath: ''
            }
        },
        methods: {
            async loadFile(){
                let formData = new FormData();
                formData.append('file', this.file);
                let response = await axios.post( 'file-upload/temporary-single-file',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(function(data){
                    if (data['status'] === 200){
                        console.log("SUCCESS")
                        return data;
                    }
                }).catch(function(){
                    console.log('FAILURE!!');
                });
                console.log(response['data']['uploadedPath']);
                this.currentFilePath = response['data']['uploadedPath'];
            },
            handleFileUpload(){
                this.file = this.$refs.file.files[0]
            }
        }
    }


</script>

<style scoped>
    #leftPanel{

    }
    #rightPanel{

    }
</style>
