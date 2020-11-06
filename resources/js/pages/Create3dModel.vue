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
            <div id="infoContainer" class="collapsible" v-bind:class="infoContainerClass">
                <div class="form-group">
                    <label for="fileName">Name:</label>
                    <input class="form-control" id="fileName" type="text" v-bind:value="fileName">
                    <small id="fileNameHelp" class="form-text text-muted">This name will also be shown in your Customizer</small>
                </div>
                <div id="materialList" class="list-group">
                    <button v-for="material in materialNames" v-bind:id="material" class="list-group-item list-group-item-action" v-on:click="highlightSelectedMaterial($event)">{{material}}</button>
                </div>
            </div>
        </div>
        <div id="rightPanel" class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
            <ModelRenderer ref="modelRenderer" v-on:rendered-new-3d-model="renderedNew3dModel" v-bind:model-path="currentFilePath"></ModelRenderer>
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
                infoContainerClass: 'd-none',
                model3d: Object,
                materialNames: [],
            }
        },
        methods: {
            highlightSelectedMaterial(event){
                this.$refs.modelRenderer.highlightSelectedMaterial(event.currentTarget.id);
                let selectedMaterial = document.querySelector('.active')
                if (selectedMaterial){
                    selectedMaterial.classList.remove('active');
                }
                event.currentTarget.classList.add('active');
            },

            renderedNew3dModel(object){
                const self = this;
                self.model3d = object;
                object.traverse( function ( node ) {
                    if ( node.isMesh ){
                        if (Array.isArray(node.material)){
                            for (const [key, material] of Object.entries(node.material)) {
                                self.addToMaterialIds(material.name)
                            }
                        }
                        else {
                            self.addToMaterialIds(node.material.name)
                        }
                    }
                });
                self.infoContainerExpand();
            },

            addToMaterialIds(value){
                if(this.materialNames.indexOf(value) === -1){
                    this.materialNames.push(value);
                }
            },

            resetInfoContainer(){
                const self = this;
                self.materialNames = [];
            },

            async loadFile(){
                const self = this;
                self.resetInfoContainer()
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
                this.infoContainerCollapse();
                this.file = this.$refs.file.files[0]
                this.fileName = this.$refs.file.files[0].name.replace(/\.[^/.]+$/, "")
            },

            loadingProgressCollapse(){
                this.uploadProgressClass = 'collapsed';
            },

            loadingProgressExpand(){
                this.uploadProgressClass = 'expandedProgressBar';
            },

            infoContainerCollapse(){
                this.infoContainerClass = 'd-none'
            },

            infoContainerExpand(){
                this.infoContainerClass = 'd-block'
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
        transition: max-height 2s ease-out;
    }

    .expandedProgressBar{
        max-height: 200px;
    }

</style>
