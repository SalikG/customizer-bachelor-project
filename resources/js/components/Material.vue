<template>
    <div class="material-container">
        <div class="material-header text-left mb-0 d-flex align-items-center"
             v-bind:id="material.id"
             v-on:mouseover="isHovered = true"
             v-on:mouseleave="isHovered = false">
            <div class="flex-grow-1"
                 v-on:click="handleMaterialHeaderClick()"
                 data-toggle="collapse"
                 v-bind:data-target="'#materialBody' + material.id"
                 aria-expanded="false"
                 v-bind:aria-controls="'materialBody' + material.id">
                <input v-bind:id="'materialDisplayNameInput' + material.id"
                       v-on:keyup.enter="$event.target.blur()"
                       v-on:blur="updateMaterialDisplayName()"
                       class="disabled-input display-name-input"
                       disabled
                       v-model="material.display_name" />
                <small id="materialName" class="text-muted">({{material.material_name}})</small>
            </div>
            <div class="dropright" v-if="isHovered || focusedMaterialId === material.id" id="materialDropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h" ></i>
                <div class="dropdown-menu" aria-labelledby="materialDropdownMenu">
                    <button class="dropdown-item"
                            type="button"
                            v-on:click="handleMaterialRenameClick(material.id)">Rename</button>
                    <button class="dropdown-item"
                            v-on:click="handleSettingsOpenClick(material.id)"
                            type="button">Settings</button>
                    <button class="dropdown-item" type="button">Delete</button>
                </div>
            </div>
            <div class="text-right"
                 v-on:click="handleMaterialHeaderClick(material.id)"
                 data-toggle="collapse"
                 v-bind:data-target="'#materialBody' + material.id"
                 aria-expanded="true"
                 v-bind:aria-controls="'materialBody' + material.id">
                <i class="fas fa-angle-down ml-3" v-bind:class="{'icon-turn': getFocusedMaterialId === material.id}"></i>
            </div>
        </div>
        <div v-bind:id="'materialBody' + material.id" class="collapse" v-bind:aria-labelledby="material.id" data-parent="#materialAccordion">
            <div class="material-body">
                <div v-for="textureCategory in material.texture_categories" class="texture-category">
                    <div class="header">
                        <input class="texture-category-name"
                               type="text"
                               v-on:keyup.enter="$event.target.blur()"
                               v-on:blur="updateTextureCategoryName(textureCategory.id)"
                               v-model="textureCategory.name">
                    </div>
                    <div class="textures-container row">
                        <div class="texture col-4 col-sm-4 col-md-2 col-lg-2" v-for="texture in textureCategory.textures" v-on:click="$emit('applyTextureToModel', material.material_name, texture)">
                            <img v-bind:src="texture.icon_path" alt="icon not found">
                            <p>{{texture.name}}</p>
                        </div>
                        <div class="add-texture col-4 col-sm-4 col-md-2 col-lg-2">
                            <div v-on:click="handleAddTextureClick(textureCategory.id)">
                                <p>+</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="texture-category add-texture-category">
                    <div class="header">
                        <input v-bind:id="'newTextureCategoryNameInput' + material.id"
                               class="texture-category-name"
                               type="text"
                               placeholder="New texture category"
                               v-model="newTextureCategoryName"
                               v-on:keyup.enter="createTextureCategory()"
                               v-on:blur="newTextureCategoryName = ''"/>
                    </div>
                </div>
                <div v-bind:id="'settings' + material.id" class="material-settings-container collapse">
                    <div class="header">
                        <p>Settings</p>
                    </div>
                    <div class="settings-body">
                        <div class="form-group">
                            <label for="textureScaling">Texture scaling:</label>
                            <div>
                                <input step="0.01" min="0" v-model="textureScaling" class="form-control" type="number" id="textureScaling" name="textureScaling">
                                <small id="textureScalingHelp" class="form-text text-muted">1 equals 100%</small>
                            </div>
                        </div>
                        <button class="btn btn-primary"
                                v-on:click="updateSettings()"
                                type="button"
                                data-toggle="collapse"
                                v-bind:data-target="'#settings' + material.id"
                                aria-expanded="false"
                                v-bind:aria-controls="'settings' + material.id">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Material",
    props: {
        material: {
            type: Object,
            required: true,
        },
        focusedMaterialId: '',
        modelId: {
            type: Number,
            required: true
        }
    },
    data(){
        return{
            newTextureCategoryName: '',
            isHovered: false,
        }
    },
    computed: {
        getFocusedMaterialId(){
            return this.focusedMaterialId;
        },
        textureScaling: {
            get(){
                let textureScaling = parseFloat(this.material.texture_setting_repeat_u);
                return (1+(1-textureScaling))
            },
            set(newScaling){
                this.material.texture_setting_repeat_u = (1+(1-newScaling));
                this.material.texture_setting_repeat_v = (1+(1-newScaling));
            }
        },
    },
    methods: {
        //<editor-fold desc="Create">
        createTextureCategory(){
            const self = this;
            let formData = new FormData();
            formData.append('name', self.newTextureCategoryName);
            axios.post('models/' + self.modelId + '/materials/' + self.material.id + '/texture-categories', formData)
                .then((res) => {
                    if (res.status === 200){
                        self.$emit('newTextureCategory', self.material.id, JSON.parse(res.data.data));
                        let newTextureCategoryNameInput = document.getElementById('newTextureCategoryNameInput' + self.material.id);
                        newTextureCategoryNameInput.blur();
                    }
                }).catch((err) => {
                console.log(err);
            });
        },
        //</editor-fold>

        //<editor-fold desc="Update">
        updateMaterialDisplayName(){
            let materialDisplayNameInput = document.getElementById('materialDisplayNameInput' + this.material.id);
            materialDisplayNameInput.blur();
            materialDisplayNameInput.disabled = true;
            let formData = new FormData();
            formData.append('display_name', this.material.display_name);
            this.updateMaterial(formData);
        },

        updateSettings(){
            let formData = new FormData();
            formData.append('scaling', this.material.texture_setting_repeat_u);
            this.updateMaterial(formData);
        },

        updateMaterial(formData){
            const self = this;
            axios.post('/models/' + self.modelId + '/materials/' + self.material.id, formData)
                .then((res) => {

                }).catch((err) => {
                console.log('FAILURE')
            })
        },

        updateTextureCategoryName(textureCategoryId){
            const self = this;
            let formData = new FormData();
            formData.append('name', this.material.texture_categories.find(category => category.id === textureCategoryId).name);
            axios.post('models/' + self.modelId + '/materials/' + self.material.id + '/texture-categories/' + textureCategoryId, formData)
                .then((res) => {

                }).catch((err) => {
                console.log('FAILURE')
            })
        },


        //</editor-fold>

        //<editor-fold desc="Click handlers">
        handleMaterialHeaderClick(){
            // if true then clicked material is collapsing
            if (this.getFocusedMaterialId === this.material.id){
                this.$emit('focusedMaterialChanged', undefined);
                $('#settings' + this.material.id).collapse('hide')
            } else {
                // if material is expanding - showing
                const materialId = this.material.id;
                this.$emit('focusedMaterialChanged', materialId);
            }
        },

        handleMaterialRenameClick(materialId){
            let materialDisplayNameInput = document.getElementById('materialDisplayNameInput' + materialId);
            materialDisplayNameInput.disabled = false
            materialDisplayNameInput.focus()
            materialDisplayNameInput.select()
        },

        handleSettingsOpenClick(){
            const materialId = this.material.id;
            this.$emit('focusedMaterialChanged', materialId);
            $('#materialBody' + materialId).collapse('show')
            $('#settings' + materialId).collapse('show')
        },

        handleAddTextureClick(textureCategoryId){
            this.$emit('focusedTextureCategoryChanged', textureCategoryId)
            $("#addTextureModal").modal()
        },
        //</editor-fold>
    }
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/variables.scss'; // Using this should get you the variables


.material-header {
    cursor: default;

    div:not(.dropright):not(.dropdown-menu) {
        padding: 8px 15px;
    }

    .dropright{
        cursor: pointer;
    }

    .dropdown-menu {
        padding: 0 !important;
        min-width: 0 !important;
    }

    .icon-turn {
        transform: rotate(180deg);
    }

    .disabled-input {
        border: none;
        background-color: transparent;
    }

    .display-name-input {
        margin-right: 5px;
        font-weight: bold;
        color: black;
    }
}

.material-header:hover{
    background-color: rgba(255, 255, 255, 0.1) !important;
}

.material-body {
    padding: 15px 10px;
    background-color: #e5e5e5;
    .texture-category {
        margin-bottom: 10px;

        .header {
            border-bottom: 1px solid black;

            .texture-category-name {
                padding: 3px 2px;
                border: none;
                background-color: transparent;
                font-size: 1.1rem;
                font-weight: bold;
                width: auto;
                border-radius: 2px;
                margin-bottom: 2px;
            }

            .texture-category-name:focus {
                border: 0.2px solid black;
                box-shadow: inset 0 0 5px #000000;
            }

            .texture-category-name:hover {
                box-shadow: inset 0 0 5px #000000;
            }
        }

        .textures-container {
            margin: 10px 0;

            .texture {
                text-align: center;

                img {
                    width: 40px;
                    height: 40px;
                    font-size: 0.7rem;
                }

                p {
                    font-size: 0.7rem;
                    font-weight: bold;
                    color: #4a5568;
                }
            }

            .add-texture {
                div {
                    margin: auto;
                    width: 40px;
                    height: 40px;
                    border-radius: 5px;
                    text-align: center;
                    background-color: rgba(0, 0, 0, 0.1);
                    cursor: pointer;

                    p {
                        line-height: 38px;
                        margin: 0;
                        font-size: 3rem;
                    }
                }
            }
        }
    }

    .add-texture-category {
        margin-bottom: 20px !important;

        .header {
            .texture-category-name {
                font-size: 0.8rem !important;
            }
        }
    }

    .material-settings-container {
        .header {
            border-bottom: 1px solid black;
            margin-bottom: 5px;

            p {
                margin: 0;
                font-size: 1.1rem;
                font-weight: bold;
            }
        }

        .settings-body {

        }
    }
}

</style>
