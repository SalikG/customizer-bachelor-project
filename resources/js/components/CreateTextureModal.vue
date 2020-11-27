<template>
    <div class="modal fade" id="createTextureModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="font-weight-bold modal-title">New texture</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="textureName">Name</label>
                        <input class="form-control" v-model="FormData.name" type="text" name="textureName" id="textureName">
                    </div>
                    <div class="form-group">
                        <label for="textureDescription">Description</label>
                        <input class="form-control" v-model="FormData.description" type="text" name="textureDescription" id="textureDescription">
                    </div>
                    <label for="textureFileInput">Texture</label>
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" v-on:change="handleTextureFileSelect() " id="textureFileInput" ref="textureFile" class="custom-file-input"/>
                            <label id="textureFileLabel" class="custom-file-label" for="textureFileInput" aria-describedby="inputFileAddon">{{ textureFileName === '' ? 'Choose file' : textureFileName }}</label>
                        </div>
                    </div>
                    <label for="iconFileInput">Icon</label>
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" v-on:change="handleIconFileSelect()" id="iconFileInput" ref="iconFile" class="custom-file-input"/>
                            <label id="iconFileLabel" class="custom-file-label" for="iconFileInput" aria-describedby="inputFileAddon">{{ iconFileName === '' ? 'Choose file' : iconFileName }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "CreateTextureModal",
    data() {
        return {
            textureFileName: '',
            iconFileName: '',
            FormData: {
                name: '',
                description: '',
                textureFile: null,
                iconFile: null,
            }
        }
    },
    methods: {
        handleIconFileSelect(){
            this.FormData.iconFile = this.$refs.iconFile.files[0];
            this.iconFileName = this.FormData.iconFile.name.replace(/\.[^/.]+$/, "");
        },
        handleTextureFileSelect(){
            this.FormData.textureFile = this.$refs.textureFile.files[0];
            this.textureFileName = this.FormData.textureFile.name.replace(/\.[^/.]+$/, "");
        }
    }
}
</script>

<style scoped>

</style>
