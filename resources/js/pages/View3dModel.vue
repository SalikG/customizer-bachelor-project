<template>
    <div>
        <div id="topPanel" class="d-inline-flex align-items-center">
            <div class="img-wrapper">
                <img class="model-display-img" v-bind:src="model.display_img_path" v-bind:alt="model.display_img_path + ' not found'" />
                <div class="model-display-img-overlay"><i class="fas fa-edit d-none"></i></div>
            </div>
            <input type="text" class="model-name" v-on:keyup.enter="$event.target.blur()" v-on:blur="updateModelName" v-model="model.name"/>
            <button type="button" class="btn btn-outline-danger ml-auto" v-on:click="deleteModel()">Delete</button>
        </div>
        <div class="row">
            <div id="leftPanel" class="col">
                <div class="accordion" id="materialAccordion">
<!--                        <h3>No textures</h3>-->
                    <h3 class="material-category-header no-texture">No textures</h3>
                    <Material class="no-texture"
                              v-bind:class="{'shadow' : material.id === focusedMaterialId}"
                              v-for="material in getMaterialsWithNoTextures"
                              v-bind:material="material"
                              v-bind:focused-material-id="focusedMaterialId"
                              v-bind:key="material.id"
                              v-bind:model-id="model.id"
                              @focusedMaterialChanged="focusedMaterialChanged"
                              @focusedTextureCategoryChanged="focusedTextureCategoryChanged"
                              @newTextureCategory="newTextureCategory"
                              @applyTextureToModel="applyTextureToModel"
                              @textureScalingChanged="textureScalingChanged"></Material>
<!--                        <h3>Customizable</h3>-->
                    <h3 class="material-category-header customizable">Customizable</h3>
                    <Material class="customizable"
                              v-bind:class="{'shadow' : material.id === focusedMaterialId}"
                              v-for="material in getMaterialsWithMultipleTextures"
                              v-bind:material="material"
                              v-bind:focused-material-id="focusedMaterialId"
                              v-bind:key="material.id"
                              v-bind:model-id="model.id"
                              @focusedMaterialChanged="focusedMaterialChanged"
                              @focusedTextureCategoryChanged="focusedTextureCategoryChanged"
                              @newTextureCategory="newTextureCategory"
                              @textureScalingChanged="textureScalingChanged"
                              @applyTextureToModel="applyTextureToModel"></Material>
<!--                        <h3>Fixed (one texture)</h3>-->
                    <h3 class="material-category-header fixed-texture">Fixed (one texture)</h3>
                    <Material class="fixed-texture"
                              v-bind:class="{'shadow' : material.id === focusedMaterialId}"
                              v-for="material in getMaterialsWithOneTexture"
                              v-bind:material="material"
                              v-bind:focused-material-id="focusedMaterialId"
                              v-bind:key="material.id"
                              v-bind:model-id="model.id"
                              @focusedMaterialChanged="focusedMaterialChanged"
                              @focusedTextureCategoryChanged="focusedTextureCategoryChanged"
                              @newTextureCategory="newTextureCategory"
                              @textureScalingChanged="textureScalingChanged"
                              @applyTextureToModel="applyTextureToModel"></Material>
                </div>
            </div>
            <div id="rightPanel" class="col">
                <ModelRenderer class="sticky-top"
                               ref="modelRenderer"
                               v-bind:model-path="model.file_path"
                               v-bind:canvas-container-unique-id="'canvasContainer'"
                               v-bind:background="0xEEEEEE"
                               v-bind:texture-to-apply-by-material-name="textureToApplyByMaterialName"
                               v-bind:texture-scaling-to-apply-by-material-name="textureScalingToApplyByMaterialName"></ModelRenderer>
            </div>

<!----------------------------------------    MODALS    ---------------------------------------------->
            <div class="modal fade" id="addTextureModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered w-fit-content">
                    <div class="modal-content w-auto">
                        <div class="modal-body d-inline-flex justify-content-between">
                            <button class="btn btn-secondary mr-2" type="button" data-dismiss="modal" v-on:click="handleNewTextureClick()">Upload new texture</button>
                            <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal" v-on:click="handleExistingTexturesClick()">Choose existing texture</button>
                        </div>
                    </div>
                </div>
            </div>
            <CreateTextureModal @newTexture="newTexture"
                                v-bind:model-id="model.id"
                                v-bind:material-id="focusedMaterialId"
                                v-bind:texture-category-id="focusedTextureCategoryId"></CreateTextureModal>
            <div class="modal fade" id="textureListModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="font-weight-bold modal-title">Textures</h5>
                        </div>
                        <div class="modal-body">
                            <TextureList @addExistingTextureClicked="addExistingTextureClicked" v-bind:textures="filteredExistingTextures"></TextureList>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ModelRenderer from "../components/ModelRenderer";
import CreateTextureModal from "../components/CreateTextureModal";
import Material from "../components/Material";
import TextureList from "../components/TextureList";

export default {
        name: "View3dModel",
        components: {ModelRenderer, CreateTextureModal, Material, TextureList},
        props: {
            model: {
                type: Object,
                required: true,
            }
        },
        data(){
            return {
                newTextureCategoryName: '',
                focusedMaterialId: null,
                focusedTextureCategoryId: null,
                existingTextures: [],
                textureToApplyByMaterialName: {},
                textureScalingToApplyByMaterialName: {},
                meshMaterials: [],
            }
        },
        mounted() {
            const self = this;
            self.getModelMeshMaterialData();
            self.getExistingTextures()
            $('.modal').on('shown.bs.modal', () => {
                self.$root.isModalOpen = true;
            }).on('hidden.bs.modal', () => {
                self.$root.isModalOpen = false;
            })
        },
        computed: {
            filteredExistingTextures(){
                const self = this;
                if (self.focusedMaterialId !== null && self.focusedTextureCategoryId !== null){
                    const alreadyAddedTextures = self.meshMaterials.find(material => material.id === self.focusedMaterialId).texture_categories.map(o => o.textures).flat()
                    return this.existingTextures.filter(texture => alreadyAddedTextures.every(alreadyAddedTexture => alreadyAddedTexture.id !== texture.id));
                }
                return this.existingTextures;
            },
            getMaterialsWithOneTexture(){
                return this.meshMaterials.filter((material) => {
                    let numOfTextures = 0;
                    if (material.texture_categories.length !== 0) {
                        material.texture_categories.forEach((textureCategory) => {
                            numOfTextures += textureCategory.textures.length;
                        })
                    }
                    if (numOfTextures === 1){
                        return material;
                    }
                });
            },
            getMaterialsWithMultipleTextures(){
                return this.meshMaterials.filter((material) => {
                    let numOfTextures = 0;
                    if (material.texture_categories.length !== 0) {
                        material.texture_categories.forEach((textureCategory) => {
                            numOfTextures += textureCategory.textures.length;
                        })
                    }
                    if (numOfTextures > 1){
                        return material;
                    }
                });
            },

            getMaterialsWithNoTextures(){
                return this.meshMaterials.filter((material) => {
                    let numOfTextures = 0;
                    if (material.texture_categories.length !== 0) {
                        material.texture_categories.forEach((textureCategory) => {
                            numOfTextures += textureCategory.textures.length;
                        })
                    }
                    if (numOfTextures === 0){
                        return material;
                    }
                });
            }
        },
        methods: {
            //<editor-fold desc="Get">
            async getModelMeshMaterialData(){
                const self = this;
                await axios.get('/models/' + self.model.id + '/materials').then((res) => {
                    if (res.status === 200){
                        self.meshMaterials = res.data;
                    }
                }).catch((err) => {
                    console.log(err)
                })
            },

            async getExistingTextures(){
                const self = this;
                await axios.get('/textures')
                    .then((res) => {
                        if (res.status === 200 && res.data['textures']){
                            self.existingTextures = res.data['textures'];
                        }
                    }).catch((err) => {
                    console.log('SERVER ERROR')
                })
            },
            //</editor-fold>

            //<editor-fold desc="Update">
            updateModelName(){
                let formData = new FormData();
                formData.append('name', this.model.name);
                this.updateModel(formData);
            },

            updateModel(formData){
                const self = this;
                axios.post('models/' + self.model.id, formData)
                    .then((res) => {
                        if (res.status === 200){
                            console.log('Model update SUCCESS')
                        }
                    }).catch((err) => {
                        console.log('FAILURE');
                });
            },

            addExistingTexture(texture){
                const self = this;
                axios.post('/models/' + self.model.id + '/materials/' + self.focusedMaterialId + '/texture-categories/' + self.focusedTextureCategoryId + '/textures/' + texture.id,)
                    .then((res) => {
                        $("#textureListModal").modal('hide');
                        if (res.status === 200){
                            self.newTexture(self.focusedMaterialId, self.focusedTextureCategoryId, texture)
                        }
                    }).catch((err) => {
                    console.log('SERVER ERROR')
                })
            },
            //</editor-fold>

            //<editor-fold desc="Delete">
            async deleteModel(){
                const self = this;
                await axios.delete('/models/' + self.model.id)
                    .then((res) => {
                        if (res.status === 200){
                            self.$router.replace('/models');
                        }
                    }).catch((err) => {
                    console.log('SERVER ERROR');
                })
            },
            //</editor-fold>

            //<editor-fold desc="Event handlers from child components">
            newTextureCategory(materialId, textureCategory){
                this.meshMaterials.find(material => material.id === materialId).texture_categories.push(textureCategory);
            },

            newTexture(materialId, textureCategoryId, texture){
                this.meshMaterials.find(material => material.id === materialId)
                    .texture_categories
                    .find(textureCategory => textureCategory.id === textureCategoryId)
                    .textures
                    .push(texture);
            },

            focusedMaterialChanged(materialId){
                this.focusedMaterialId = materialId;
            },

            focusedTextureCategoryChanged(textureCategoryId){
                this.focusedTextureCategoryId = textureCategoryId;
            },

            addExistingTextureClicked(texture){
                this.addExistingTexture(texture)
            },

            applyTextureToModel(material, texture){
                let result = {};
                result[material.material_name] = {'texture': texture, 'material': material};
                this.textureToApplyByMaterialName = result;
            },

            textureScalingChanged(materialName, textureScalingValue){
                let result = {};
                result[materialName] = textureScalingValue;
                this.textureScalingToApplyByMaterialName = result;
            },
            //</editor-fold>

            //<editor-fold desc="Click handlers">
            handleNewTextureClick(){
                $("#createTextureModal").modal()
            },

            handleExistingTexturesClick(){
                $("#textureListModal").modal();
            }
            //</editor-fold>
        }

    }
</script>

<style lang="scss" scoped>
    @import 'resources/sass/variables.scss';

    #topPanel{
        width: 100%;
        height: 100px;
        max-height: 100px;
        padding-bottom: 1rem;
        border-bottom: 1px solid black;
        margin-bottom: 5px;
        .model-name{
            margin: 0 10px 0 10px;
            padding: 10px 15px;
            border: none;
            background-color: transparent;
            font-size: 1.5rem;
            font-weight: bold;
            width: auto;
            border-radius: 5px;
        }
        .model-name:focus{
            border: 0.2px solid black;
            box-shadow: inset 0 0 5px #000000;
        }
        .model-name:hover{
            box-shadow: inset 0 0 5px #000000;
        }
        .img-wrapper {
            position: relative;
            height: 100%;
            .model-display-img{
                height: 100%;
            }
            .model-display-img-overlay{
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                cursor: pointer;
            }
            .model-display-img-overlay:hover{
                background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
                i{
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    font-size: 1.2rem;
                    color: white;
                    display: block !important;
                    padding-right: 10px;
                    padding-bottom: 10px;
                }
            }
        }
    }

    #leftPanel{
        .material-category-header{
            width: 100%;
            text-align: center;
            padding: 5px;
            color: white;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 4px;
            cursor: default;
        }

        .material-category-header.no-texture{
            background-color: $red !important;
        }
        .no-texture{
            background-color: rgba($red, 0.7) !important;
        }

        .material-category-header.customizable{
            background-color: $teal;
        }
        .customizable{
            background-color: rgba($teal, 0.7);
        }

        .material-category-header.fixed-texture{
            background-color: rgba(0, 0, 0, 0.5);
        }
        .fixed-texture{
            background-color: rgba(0, 0, 0, 0.2);
        }

        .material-container {
            margin-bottom: 1px;
        }

        .shadow{
            box-shadow: 0 0.1rem 1rem rgba(0, 0, 0, 1) !important;
        }
    }
</style>
