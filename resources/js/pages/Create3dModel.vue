<template>
    <div class="row">
        <div id="leftPanel" class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" id="fileInput" ref="file" v-on:change="handleFileUpload()" class="custom-file-input"/>
                    <label id="fileInputLabel" class="custom-file-label" for="fileInput" aria-describedby="inputFileAddon">{{ fileName === '' ? 'Choose file' : fileName }}</label>
                </div>
                <div class="input-group-append">
                    <button class="btn btn-secondary" id="inputFileAddon" v-on:click="loadFile()">Load</button>
                </div>
            </div>
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
                fileName: '',
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
                this.currentFilePath = response['data']['uploadedPath'];
            },
            handleFileUpload(){
                this.file = this.$refs.file.files[0]
                console.log(this.$refs.file.files[0].name);
                this.fileName = this.$refs.file.files[0].name
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
