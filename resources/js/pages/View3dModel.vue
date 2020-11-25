<template>
    <div>
        <div id="topPanel" class="d-inline-flex align-items-center">
            <div class="img-wrapper">
                <img class="model-display-img" v-bind:src="model.display_img_path" v-bind:alt="model.display_img_path + ' not found'" />
                <div class="model-display-img-overlay"><i class="fas fa-edit d-none"></i></div>
            </div>
            <input type="text" class="model-name" v-on:keyup.enter="$event.target.blur()" v-bind:value="model.name"/>
        </div>
        <div class="row">
            <div id="leftPanel" class="col">
                <div class="accordion" id="materialAccordion">
                    <div class="material-container" v-for="material in meshMaterials">
                        <div v-bind:id="material.id" class="material-header text-left mb-0 d-flex align-items-center">
                            <div class="flex-grow-1"
                                 v-on:click="handleMaterialHeaderClick(material.id)"
                                 type="button"
                                 data-toggle="collapse"
                                 v-bind:data-target="'#materialBody' + material.id"
                                 aria-expanded="true"
                                 v-bind:aria-controls="'materialBody' + material.id">
                                <input class="disabled-input display-name-input" disabled v-bind:value="material.display_name" />
                                <small id="materialName" class="text-muted">({{material.material_name}})</small>
                            </div>
                            <div class="dropright">
                                <i class="fas fa-ellipsis-h" id="materialDropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                <div class="dropdown-menu" aria-labelledby="materialDropdownMenu">
                                    <button class="dropdown-item" type="button">Rename</button>
                                    <button class="dropdown-item"
                                            v-on:click="handleSettingsOpenClick(material.id)"
                                            type="button"
                                            data-toggle="collapse"
                                            v-bind:data-target="'#settings' + material.id"
                                            aria-expanded="false"
                                            v-bind:aria-controls="'settings' + material.id">Settings</button>
                                    <button class="dropdown-item" type="button">Delete</button>
                                </div>
                            </div>
                            <div v-on:click="handleMaterialHeaderClick(material.id)"
                                 type="button"
                                 data-toggle="collapse"
                                 v-bind:data-target="'#materialBody' + material.id"
                                 aria-expanded="true"
                                 v-bind:aria-controls="'materialBody' + material.id">
                                <i class="fas fa-caret-square-down ml-3" v-bind:class="{'icon-turn': shownMaterialId === material.id}"></i>
                            </div>
                        </div>
                        <div v-bind:id="'materialBody' + material.id" class="collapse" v-bind:aria-labelledby="material.id" data-parent="#materialAccordion">
                            <div class="material-body">
                                <div v-for="textureCategory in material.texture_categories" class="texture-category">
                                    <div class="header">
                                        <input class="texture-category-name" type="text" v-bind:value="textureCategory.name">
                                    </div>
                                    <div class="textures-container row">
                                        <div class="texture col-4 col-sm-4 col-md-2 col-lg-2" v-for="texture in textureCategory.textures">
                                            <img v-bind:src="texture.icon_path" alt="icon not found">
                                            <p>{{texture.name}}</p>
                                        </div>
                                        <div class="add-texture col-4 col-sm-4 col-md-2 col-lg-2">
                                            <div>
                                                <p>+</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="texture-category add-texture-category">
                                    <div class="header">
                                        <input class="texture-category-name" type="text" placeholder="New texture category"/>
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
                                                <input step="0.01" min="0" v-bind:value="getTextureScaling(material.id)" class="form-control" type="number" id="textureScaling" name="textureScaling">
                                                <small id="textureScalingHelp" class="form-text text-muted">1 equals 100%</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-success"
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
                </div>
            </div>
            <div id="rightPanel" class="col">
<!--                <ModelRenderer ref="modelRenderer"-->
<!--                               v-bind:model-path="model.file_path"-->
<!--                               v-bind:canvas-container-unique-id="'canvasContainer'"-->
<!--                               v-bind:background="0xEEEEEE"></ModelRenderer>-->
            </div>
        </div>
    </div>
</template>

<script>
    import ModelRenderer from "../components/ModelRenderer";

    export default {
        name: "View3dModel",
        components: {ModelRenderer},
        props: {
            model: {
                type: Object,
                required: true,
                default: {
                    "id":5,
                    "company_id":1,
                    "name":"Base cap",
                    "file_path":"storage/96a3e074-3c19-4ef1-8779-0fd687ba6861/3dModels/920df18f-b9b8-4cfa-96b8-d00a5e2e2d9c.obj",
                    "display_img_path":"storage/96a3e074-3c19-4ef1-8779-0fd687ba6861/3dModelDisplayImages//920df262-09ee-43e2-afdb-b093d52398dc.png",
                    "created_at":"2020-11-20T15:26:27.000000Z",
                    "updated_at":"2020-11-20T15:26:27.000000Z"
                }
            }
        },
        data(){
            return {
                shownMaterialId: Number,
                meshMaterials: [{"id":15,"product_3d_model_id":10,"material_name":"Detail_FRONT_3812","display_name":"Detail_FRONT_3812","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},
                    {
                        "id":16,
                        "product_3d_model_id":10,
                        "material_name":"FABRIC 2_FRONT_3789",
                        "display_name":"FABRIC 2_FRONT_3789",
                        "texture_setting_wrap_s":"RepeatWrapping",
                        "texture_setting_wrap_t":"RepeatWrapping",
                        "texture_setting_repeat_u":"1.00",
                        "texture_setting_repeat_v":"1.00",
                        "created_at":"2020-11-24T10:35:33.000000Z",
                        "updated_at":"2020-11-24T10:35:33.000000Z",
                        "texture_categories":
                            [
                                {
                                    "id":1,
                                    "name":"test",
                                    "created_at":null,
                                    "updated_at":null,
                                    "pivot":{"mesh_material_id":16,"texture_category_id":1},
                                    "textures":
                                        [
                                            {"id":1,
                                                "uuid":"9ed86e80-41a0-48da-9455-5b1008bc018d",
                                                "name":"testerTexture",
                                                "description":"bla blabblalsgofmh adsgh",
                                                "file_path":"test not working",
                                                "icon_path":"test not working",
                                                "created_at":null,
                                                "updated_at":null,
                                                "pivot":{"texture_category_id":1,"texture_id":1}
                                            }
                                        ]
                                }
                            ]
                    },
                    {"id":17,"product_3d_model_id":10,"material_name":"FABRIC 2_BACK_3789","display_name":"FABRIC 2_BACK_3789","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":18,"product_3d_model_id":10,"material_name":"Brim _FRONT_3800","display_name":"Brim _FRONT_3800","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":19,"product_3d_model_id":10,"material_name":"Brim _BACK_3800","display_name":"Brim _BACK_3800","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":20,"product_3d_model_id":10,"material_name":"Material79508","display_name":"Material79508","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":21,"product_3d_model_id":10,"material_name":"Buckle_FRONT_3806","display_name":"Buckle_FRONT_3806","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":22,"product_3d_model_id":10,"material_name":"Buckle_BACK_3806","display_name":"Buckle_BACK_3806","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":23,"product_3d_model_id":10,"material_name":"Material1282830","display_name":"Material1282830","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":24,"product_3d_model_id":10,"material_name":"Band_FRONT_3817","display_name":"Band_FRONT_3817","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":25,"product_3d_model_id":10,"material_name":"Lining_FRONT_3822","display_name":"Lining_FRONT_3822","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":26,"product_3d_model_id":10,"material_name":"Material4295","display_name":"Material4295","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":27,"product_3d_model_id":10,"material_name":"Material4414","display_name":"Material4414","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]},{"id":28,"product_3d_model_id":10,"material_name":"Material4177","display_name":"Material4177","texture_setting_wrap_s":"RepeatWrapping","texture_setting_wrap_t":"RepeatWrapping","texture_setting_repeat_u":"1.00","texture_setting_repeat_v":"1.00","created_at":"2020-11-24T10:35:33.000000Z","updated_at":"2020-11-24T10:35:33.000000Z","texture_categories":[]}]

            }
        },
        mounted() {
            this.getModelMeshMaterialData();
        },
        methods: {
            getModelMeshMaterialData(){
                const self = this;
                axios.get('/models/' + self.model.id + '/materials').then((res) => {
                    if (res.status === 200){
                        self.meshMaterials = res.data;
                    }
                }).catch((err) => {
                    console.log(err)
                })
            },

            getTextureScaling(materialId){
                let textureScaling = parseFloat(this.meshMaterials.find(material => material.id === materialId).texture_setting_repeat_u);
                return (1+(1-textureScaling))
            },

            handleMaterialHeaderClick(materialId){
                // if true then clicked material is collapsing
                if (this.shownMaterialId === materialId){
                    this.shownMaterialId = undefined;
                    document.querySelector('#settings' + materialId).classList.remove('show')
                } else {
                    // if material is expanding - showing
                    this.shownMaterialId = materialId;
                }
            },
            handleSettingsOpenClick(materialId){
                this.shownMaterialId = materialId;
                $('#materialBody' + materialId).collapse('show')
            },
        }
    }
</script>

<style lang="scss" scoped>
    #topPanel{
        width: 100%;
        height: 100px;
        max-height: 100px;
        padding-bottom: 1rem;
        border-bottom: 1px solid black;
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
        .material-container{
            margin: 10px 0 10px 0;
            .material-header{
                border: 1px solid #838383;
                div:not(.dropright):not(.dropdown-menu){
                    padding: 8px 15px;
                }
                .dropdown-menu{
                    padding: 0 !important;
                    min-width: 0 !important;
                }
                .icon-turn{
                    transform: rotate(180deg);
                }
                .fa-caret-square-down{
                    font-size: 1.5rem;
                }
                .disabled-input{
                    cursor: pointer;
                    border: none;
                    background-color: transparent;
                }
                .display-name-input{
                    margin-right: 5px;
                }
            }

            .material-body{
                padding: 15px 10px;
                border-left: 1.5px solid #838383;
                border-right: 1.5px solid #838383;
                border-bottom: 1.5px solid #838383;
                .texture-category{
                    margin-bottom: 10px;
                    .header{
                        border-bottom: 1px solid black;
                        .texture-category-name{
                            padding: 3px 2px;
                            border: none;
                            background-color: transparent;
                            font-size: 1.1rem;
                            font-weight: bold;
                            width: auto;
                            border-radius: 2px;
                            margin-bottom: 2px;
                        }
                        .texture-category-name:focus{
                            border: 0.2px solid black;
                            box-shadow: inset 0 0 5px #000000;
                        }
                        .texture-category-name:hover{
                            box-shadow: inset 0 0 5px #000000;
                        }
                    }
                    .textures-container{
                        margin: 10px 0;
                        .texture{
                            img{
                                width: 40px;
                                height: 40px;
                                font-size: 0.7rem;
                            }
                            p{
                                font-size: 0.7rem;
                                font-weight: bold;
                                color: #4a5568;
                            }
                        }
                        .add-texture{
                            div{
                                width: 40px;
                                height: 40px;
                                border-radius: 5px;
                                text-align: center;
                                background-color: #E4E4E4;
                                p{
                                    line-height: 38px;
                                    margin: 0;
                                    font-size: 3rem;
                                }
                            }
                        }
                    }
                }
                .add-texture-category{
                    margin-bottom: 20px !important;
                    .header{
                        .texture-category-name{
                            font-size: 0.8rem !important;
                        }
                    }
                }
                .material-settings-container{
                    .header{
                        border-bottom: 1px solid black;
                        margin-bottom: 5px;
                        p{
                            margin: 0;
                            font-size: 1.1rem;
                            font-weight: bold;
                        }
                    }
                    .settings-body{

                    }
                }
            }
        }
    }
</style>
