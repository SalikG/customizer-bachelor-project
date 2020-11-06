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
            <div id="uploadProgressBar" class="collapsible" v-bind:class="uploadProgressClass">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar"
                         v-bind:aria-valuenow="uploadProgress"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         v-bind:style="{width: uploadProgress + '%'}">{{uploadProgress}} %</div>
                </div>
            </div>
            <div id="infoContainer">

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
                currentFilePath: '',
                isUploading3dModel: false,
                uploadProgress: 0,
                uploadProgressClass: 'collapsed',
            }
        },
        methods: {
            async loadFile(){
                const self = this;
                self.uploadProgress = 0;
                self.loadingProgressExpand();
                let formData = new FormData();
                formData.append('file', self.file);
                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        self.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                };
                let response = await axios.post( 'file-upload/temporary-single-file',
                    formData, config,
                ).then(function(data){
                    if (data['status'] === 200){
                        console.log("SUCCESS")
                        return data;
                    }
                }).catch(function(){
                    console.log('FAILURE!!');
                });
                self.currentFilePath = response['data']['uploadedPath'];
                self.loadingProgressCollapse();
            },

            handleFileUpload(){
                this.file = this.$refs.file.files[0]
                this.fileName = this.$refs.file.files[0].name
            },

            loadingProgressCollapse(){
                this.uploadProgressClass = 'collapsed';
            },

            loadingProgressExpand(){
                this.uploadProgressClass = 'expanded';
            }
        }
    }


</script>

<style scoped>
    .collapsed{
        max-height: 0;
    }
    .collapsible{
        position: relative;
        overflow: hidden;
        height: auto;
        transition: max-height 2s ease-in-out;
    }

    .expanded{
        max-height: 200px;
    }
</style>
