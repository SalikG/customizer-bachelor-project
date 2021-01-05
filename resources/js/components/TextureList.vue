<template>

    <div id="textureList" class="row">
        <div class="texture-container col-3 col-sm-3 col-md-2 col-lg-2" v-for="texture in textures" v-on:click="$emit('addExistingTextureClicked', texture)">
            <img v-bind:src="texture.icon_path" alt="icon not found">
            <p>{{texture.name}}</p>
        </div>
    </div>

</template>

<script>
export default {
    name: "TextureList",
    props: {
        textures: {
            type: Array,
            required: true,
        }
    },
}
</script>

<style lang="scss" scoped>
    .texture-container{
        text-align: center;
        img{
            margin: auto;
            width: 100%;
            max-width: 50px;
        }
    }

    .texture-container:hover{
        cursor: pointer;
        p{
            font-weight: bold;
        }
    }
</style>
