<template>
    <div id="canvasContainer">
        <div v-bind:class="[isLoading3dModel ? activeClass : noneActiveClass,]">
            <div class="dot-pulse"></div>
        </div>
    </div>
</template>

<script>
    import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js';
    import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

    export default {
        name: "ModelRenderer",
        props: ['modelPath'],
        data(){
            return {
                isLoading3dModel: false,
                loading3dModelProgress: 0,
                activeClass: 'activeLoading',
                noneActiveClass: 'd-none',
                camera,
                scene,
                renderer,
                controls,
                modelObject,
                orbitControls,
                maxWidth,
                maxHeight,
            }
        },
        mounted: function (){
            this.init();
            this.animate();
        },
        watch: {
            modelPath: function (newPath, oldPath){
                this.isLoading3dModel = true;
                this.update3dModel(newPath);
                this.isLoading3dModel = false;
            }
        },
        methods: {
            init(){
                this.maxWidth = document.getElementById('canvasContainer').clientWidth;
                this.maxHeight = this.maxWidth - 100;
                this.renderer = new THREE.WebGLRenderer();
                this.scene = new THREE.Scene();
                this.camera = new THREE.PerspectiveCamera( 45, this.maxWidth / this.maxHeight, 1, 10000 );
                this.orbitControls = new OrbitControls( this.camera, this.renderer.domElement );
                this.scene.background = new THREE.Color( 0xEEEEEE );
                this.renderer.setSize(this.maxWidth, this.maxHeight);
                this.controls = this.orbitControls;
                this.camera.position.set(0, 2, 500);
                this.controls.update();
                document.getElementById('canvasContainer').appendChild(this.renderer.domElement);
            },

            update3dModel(filePath){
                const self = this;
                self.cleanResetScene();

                const loader = new OBJLoader();
                self.modelObject = loader.load(
                    // resource URL
                    filePath,
                    // called when resource is loaded
            function( object ) {
                        let box = new THREE.Box3().setFromObject( object );
                        let center = new THREE.Vector3();
                        box.getCenter( center );
                        object.position.sub( center );

                        // For any meshes in the model, add our material.
                        object.traverse( function ( node ) {
                            if ( node.isMesh ){
                                if (Array.isArray(node.material)) {
                                    for (const [key, material] of Object.entries(node.material)) {
                                        const color = new THREE.Color(0x555555);
                                        material.color.set(color);
                                    }
                                }
                                else {
                                    const color = new THREE.Color(0x555555);
                                    node.material.color.set(color);
                                }
                            }
                        });
                        self.scene.add( object );
                        self.setLighting(object);
                    },
                    // called when loading is in progresses
            function ( xhr ) {
                        console.log( ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
                    },
                    // called when loading has errors
            function ( error ) {
                        console.log( error );
                    }
                );
            },

            cleanResetScene(){
                this.removeAllSceneChildren();
                this.resetCamera();
            },

            removeAllSceneChildren(){
                for( let i = this.scene.children.length - 1; i >= 0; i--) {
                    let obj = this.scene.children[i];
                    this.scene.remove(obj);
                }
            },

            resetCamera(){
                this.camera.position.set(0, 2, 500);
                this.controls.update();
            },

            setLighting(object){
                let spotLight1 = new THREE.SpotLight(0xFFFFFF, 1, 700)
                let spotLight2 = new THREE.SpotLight(0xFFFFFF, 1, 700)
                let spotLight3 = new THREE.SpotLight(0xFFFFFF, 1, 700)
                let spotLight4 = new THREE.SpotLight(0xFFFFFF, 1, 700)

                spotLight1.position.set( 0, 200, 300 );
                spotLight1.target = object;
                this.scene.add(spotLight1);
                // let pointLightHelper1 = new THREE.SpotLightHelper( spotLight1 );
                // scene.add( pointLightHelper1 );

                spotLight2.position.set( 0, 200, -300 );
                spotLight2.target = object;
                this.scene.add(spotLight2);
                // let pointLightHelper2 = new THREE.SpotLightHelper( spotLight2 );
                // scene.add( pointLightHelper2 );

                spotLight3.position.set( 300, 200, 0 );
                spotLight3.target = object;
                this.scene.add(spotLight3);
                // let pointLightHelper3 = new THREE.SpotLightHelper( spotLight3 );
                // scene.add( pointLightHelper3 );

                spotLight4.position.set( -300, 200, 0 );
                spotLight4.target = object;
                this.scene.add(spotLight4);
                // let pointLightHelper4 = new THREE.SpotLightHelper( spotLight4 );
                // scene.add( pointLightHelper4 );

                //light on all objects
                const ambientLight = new THREE.AmbientLight( 0xFFFFFF, 1.5 ); // soft white light
                this.scene.add( ambientLight );
            },

            animate() {
                requestAnimationFrame(this.animate);
                this.controls.update();
                this.renderer.render(this.scene, this.camera);
            },
        }
    }

    let camera, scene, renderer, controls, modelObject, orbitControls, maxWidth, maxHeight;


</script>

<style lang="scss" scoped>
@import "../../sass/variables.scss";

.activeLoading{
    display: block;
    position: absolute;
    top: 50%;
    left: 50%;
}

.displayNone{
    display: none;
}

.dot-pulse {
    position: relative;
    left: -9999px;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    background-color: $secondary;
    color: $secondary;
    box-shadow: 9999px 0 0 -5px $secondary;
    animation: dotPulse 1.5s infinite linear;
    animation-delay: .25s;
}

.dot-pulse::before, .dot-pulse::after {
    content: '';
    display: inline-block;
    position: absolute;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    background-color: $secondary;
    color: $secondary;
}

.dot-pulse::before {
    box-shadow: 9984px 0 0 -5px $secondary;
    animation: dotPulseBefore 1.5s infinite linear;
    animation-delay: 0s;
}

.dot-pulse::after {
    box-shadow: 10014px 0 0 -5px $secondary;
    animation: dotPulseAfter 1.5s infinite linear;
    animation-delay: .5s;
}

@keyframes dotPulseBefore {
    0% {
        box-shadow: 9984px 0 0 -5px $secondary;
    }
    30% {
        box-shadow: 9984px 0 0 2px $secondary;
    }
    60%,
    100% {
        box-shadow: 9984px 0 0 -5px $secondary;
    }
}

@keyframes dotPulse {
    0% {
        box-shadow: 9999px 0 0 -5px $secondary;
    }
    30% {
        box-shadow: 9999px 0 0 2px $secondary;
    }
    60%,
    100% {
        box-shadow: 9999px 0 0 -5px $secondary;
    }
}

@keyframes dotPulseAfter {
    0% {
        box-shadow: 10014px 0 0 -5px $secondary;
    }
    30% {
        box-shadow: 10014px 0 0 2px $secondary;
    }
    60%,
    100% {
        box-shadow: 10014px 0 0 -5px $secondary;
    }
}

</style>
