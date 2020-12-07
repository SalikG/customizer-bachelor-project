<template>
    <div class="row">
        <div id="leftPanel" class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
            <h5>3d File</h5>
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" id="fileInput" ref="modelFile" v-on:change="handle3dModelFileUpload()" class="custom-file-input"/>
                    <label id="fileInputLabel" class="custom-file-label" for="fileInput" aria-describedby="inputFileAddon">{{ modelFileName === '' ? 'Choose file' : modelFileName }}</label>
                </div>
                <p v-for="error in modelFileValidationErrors" class="errorMessage pb-3">{{ error }}</p>
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
            <div id="infoContainer" class="collapsible" v-bind:class="isLoadedControllerClass">
                <div class="form-group">
                    <label for="fileName">Name:</label>
                    <input class="form-control" id="fileName" type="text" v-model="FormData.displayName">
                    <small id="fileNameHelp" class="form-text text-muted">This name will also be shown in your Customizer</small>
                </div>
                <div class="form-group">
                    <label for="displayImgFileInput">Display image:</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" id="displayImgFileInput" ref="displayImgFile" v-on:change="handleDisplayImgFileUpload()" class="custom-file-input"/>
                            <label id="displayImgFileInputLabel" class="custom-file-label" for="displayImgFileInput" aria-describedby="inputFileAddon">{{ displayImgFileName === '' ? 'Choose file' : displayImgFileName }}</label>
                        </div>
                    </div>
                    <small id="displayImgFileHelp" class="form-text text-muted">This will be shown in your overview of 3d models</small>
                    <p v-for="error in displayImgFileValidationErrors" class="errorMessage pb-3">{{ error }}</p>
                </div>
                <div id="materialList" class="list-group">
                    <label for="materialList">Materials <small>(click to highlight)</small>:</label>
                    <button v-for="material in FormData.materialNames" v-bind:id="material" class="list-group-item list-group-item-action" v-on:click="highlightSelectedMaterial($event)">{{material}}</button>
                </div>
            </div>
        </div>
        <div id="rightPanel" class="col-sm-7 col-md-7 col-lg-7 col-xl-7">
            <ModelRenderer ref="modelRenderer"
                           v-on:rendered-new-3d-model="renderedNew3dModel"
                           v-bind:model-path="FormData.currentFilePath"
                           v-bind:canvas-container-unique-id="'canvasContainer'"
                           v-bind:isUploading3dModel="isUploading3dModel"
                           v-bind:background="0xEEEEEE"></ModelRenderer>
            <p v-for="error in serverValidationErrors" class="errorMessage pb-3">{{ error }}</p>
            <button class="btn btn-success btn-block"
                    v-on:click="save3dModel"
                    v-bind:class="isLoadedControllerClass">Save</button>
<!--                    v-bind:disabled="!formIsValid" -->
        </div>

        <div class="modal fade" id="saveCompleteModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body d-flex flex-column">
                        <div class="d-flex justify-content-center">
                            <div class="circle-loader">
                                <div class="checkmark draw"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h2>Save complete</h2>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <router-link :to="{name: 'create-3d-model', query: { reloadId: reloadId}}">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Add another 3d model</button>
                        </router-link>
                        <router-link :to="{name: 'models'}">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Go to overview</button>
                        </router-link>
                    </div>
                </div>
            </div>
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
                reloadId: Math.floor(Math.random() * 100),
                modelFile: '',
                modelFileName: '',
                displayImgFileName: '',
                FormData: {
                    displayName: '',
                    currentFilePath: '',
                    displayImgFile: '',
                    materialNames: [],
                },
                uploadProgress: 0,
                uploadProgressClass: 'collapsed',
                isLoadedControllerClass: 'd-none',
                model3d: Object,
                modelFileValidationErrors: [],
                displayImgFileValidationErrors: [],
                serverValidationErrors: [],
            }
        },
        computed: {
            isUploading3dModel(){
                return !(this.uploadProgress === 0 || this.uploadProgress === 100);
            },
            formIsValid(){
                return this.modelFileValidationErrors.length === 0 &&
                    this.displayImgFileValidationErrors.length === 0 &&
                    this.FormData.displayImgFile !== '' &&
                    this.FormData.currentFilePath !== '' &&
                    this.FormData.displayName !== '';
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
                if(this.FormData.materialNames.indexOf(value) === -1){
                    this.FormData.materialNames.push(value);
                }
            },

            resetInfoContainer(){
                const self = this;
                self.FormData.materialNames = [];
                self.infoContainerCollapse()
            },

            async loadFile(){
                const self = this;
                self.modelFileValidationErrors = [];
                self.serverValidationErrors = [];
                self.resetInfoContainer()
                self.uploadProgress = 0;
                self.loadingProgressExpand();
                let formData = new FormData();
                formData.append('file', self.modelFile);
                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        self.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                };
                await axios.post( 'file-upload/temporary-3d-model-single-file',
                    formData, config,
                ).then(function(res){
                    if (res.status === 200){
                        console.log("SUCCESS")
                        self.FormData.currentFilePath = res['data']['uploadedPath'];
                        self.loadingProgressCollapse();
                    }
                }).catch(function(err){
                    if (err.response.status === 415){
                        self.modelFileValidationErrors.push(err.response.statusText);
                        self.loadingProgressCollapse();
                    }
                });

            },

            async save3dModel(){
                const self = this;
                self.serverValidationErrors = [];
                let formData = new FormData();
                formData.append('name', self.FormData.displayName);
                formData.append('tempFilePath', self.FormData.currentFilePath);
                formData.append('displayImgFile', self.FormData.displayImgFile);
                formData.append('meshMaterialNames', JSON.stringify(self.FormData.materialNames));

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        console.log(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                    }
                };
                await axios.post('/file-upload/save-3d-model-single-file', formData, config)
                    .then((res) => {
                        if(res.status === 200){
                            $("#saveCompleteModal").modal()
                            document.querySelector('.circle-loader').classList.toggle('load-complete')
                            $('.checkmark').toggle()
                        }
                    }).catch((err) => {
                        if (err.response.status === 422){
                            Object.keys(err.response.data.errors).forEach((keyName) => {
                                err.response.data.errors[keyName].forEach((errorMsg) => {
                                    self.serverValidationErrors.push(errorMsg)
                                })
                            })
                        }
                    })
            },

            handle3dModelFileUpload(){
                this.infoContainerCollapse();
                this.modelFile = this.$refs.modelFile.files[0];
                this.modelFileName = this.$refs.modelFile.files[0].name.replace(/\.[^/.]+$/, "");
                this.FormData.displayName = this.modelFileName;
                this.loadFile()
            },

            handleDisplayImgFileUpload(){
                this.displayImgFileValidationErrors = [];
                if(this.validateImg(this.$refs.displayImgFile.files[0])){
                    this.FormData.displayImgFile = this.$refs.displayImgFile.files[0];
                    this.displayImgFileName = this.$refs.displayImgFile.files[0].name.replace(/\.[^/.]+$/, "");
                }
            },

            validateImg(file){
                let isValid = (file.type === 'image/png') ? true :
                    (file.type === 'image/jpg') ? true :
                    (file.type === 'image/jpeg');
                if (!isValid) {
                    this.displayImgFileValidationErrors.push('Only jpg, jpeg or png files allowed!');
                }
                return isValid;
            },

            loadingProgressCollapse(){
                this.uploadProgressClass = 'collapsed';
            },

            loadingProgressExpand(){
                this.uploadProgressClass = 'expandedProgressBar';
            },

            infoContainerCollapse(){
                this.isLoadedControllerClass = 'd-none'
            },

            infoContainerExpand(){
                this.isLoadedControllerClass = 'd-block'
            }
        }
    }



</script>

<style lang="scss" scoped>
    .errorMessage{
        width: 100%;
        margin-top: .25rem;
        font-size: 85%;
        color: #dc3545;
        margin-bottom: 0;
    }

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

    $brand-success: #5cb85c;
    $loader-size: 7em;
    $check-height: $loader-size/2;
    $check-width: $check-height/2;
    $check-left: ($loader-size/6 + $loader-size/12);
    $check-thickness: 3px;
    $check-color: $brand-success;

    .circle-loader {
        margin-bottom: $loader-size/2;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-left-color: $check-color;
        animation: loader-spin 1.2s infinite linear;
        position: relative;
        display: inline-block;
        vertical-align: top;
        border-radius: 50%;
        width: $loader-size;
        height: $loader-size;
    }

    .load-complete {
        -webkit-animation: none;
        animation: none;
        border-color: $check-color;
        transition: border 500ms ease-out;
    }

    .checkmark {
        display: none;

    &.draw:after {
         animation-duration: 800ms;
         animation-timing-function: ease;
         animation-name: checkmark;
         transform: scaleX(-1) rotate(135deg);
     }

    &:after {
         opacity: 1;
         height: $check-height;
         width: $check-width;
         transform-origin: left top;
         border-right: $check-thickness solid $check-color;
         border-top: $check-thickness solid $check-color;
         content: '';
         left: $check-left;
         top: $check-height;
         position: absolute;
     }
    }

    @keyframes loader-spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes checkmark {
        0% {
            height: 0;
            width: 0;
            opacity: 1;
        }
        20% {
            height: 0;
            width: $check-width;
            opacity: 1;
        }
        40% {
            height: $check-height;
            width: $check-width;
            opacity: 1;
        }
        100% {
            height: $check-height;
            width: $check-width;
            opacity: 1;
        }
    }
</style>
