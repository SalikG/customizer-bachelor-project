<template>
    <div id="canvasContainer">

    </div>
</template>

<script>
    import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js';
    import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

    const supported3dFiles = [
        'obj',
    ]

    export default {
        name: "ModelRenderer",
        props: ['modelPath'],
        mounted: function (){
            init();
            animate();
        },
        watch: {
            modelPath: function (newPath, oldPath){
                console.log('Prop changed: ', newPath, ' | was: ', oldPath)
                update3dModel(newPath);
            }
        }
    }

    let camera, scene, renderer, controls, modelObject, orbitControls, maxWidth, maxHeight;

    function init(){
        maxWidth = document.getElementById('canvasContainer').clientWidth;
        maxHeight = maxWidth - 100;

        renderer = new THREE.WebGLRenderer();
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera( 45, maxWidth / maxHeight, 1, 10000 );
        orbitControls = new OrbitControls( camera, renderer.domElement );
        scene.background = new THREE.Color( 0xEEEEEE );

        renderer.setSize(maxWidth, maxHeight);
        document.getElementById('canvasContainer').appendChild(renderer.domElement);

        controls = orbitControls;

        //controls.update() must be called after any manual changes to the camera's transform
        camera.position.set(0, 2, 500);
        controls.update();
    }

    function update3dModel(filePath){
        cleanResetScene();
        // let fileType = filePath.split(/[#?]/)[0].split('.').pop().trim();
        // if (!validate3dModel(fileType)){console.log('wrong file path'); return;}

        const loader = new OBJLoader();
        modelObject = loader.load(
            // resource URL
            filePath,
            // called when resource is loaded
            function ( object ) {
                let box = new THREE.Box3().setFromObject( object );
                let center = new THREE.Vector3();
                box.getCenter( center );
                object.position.sub( center );
                console.log(object)
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
                scene.add( object );
                setLighting(object);
                console.log(object)

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
    }

    // function validate3dModel(fileType){
    //     return supported3dFiles.includes(fileType);
    // }

    function cleanResetScene(){
        removeAllSceneChildren();
        resetCamera();
    }

    function removeAllSceneChildren(){
        for( let i = scene.children.length - 1; i >= 0; i--) {
            let obj = scene.children[i];
            scene.remove(obj);
        }
    }

    function resetCamera(){
        camera.position.set(0, 2, 500);
        controls.update();
    }

    function setLighting(object){
        let spotLight1 = new THREE.SpotLight(0xFFFFFF, 1, 700)
        let spotLight2 = new THREE.SpotLight(0xFFFFFF, 1, 700)
        let spotLight3 = new THREE.SpotLight(0xFFFFFF, 1, 700)
        let spotLight4 = new THREE.SpotLight(0xFFFFFF, 1, 700)

        spotLight1.position.set( 0, 200, 300 );
        spotLight1.target = object;
        scene.add(spotLight1);
        let pointLightHelper1 = new THREE.SpotLightHelper( spotLight1 );
        scene.add( pointLightHelper1 );

        spotLight2.position.set( 0, 200, -300 );
        spotLight2.target = object;
        scene.add(spotLight2);
        let pointLightHelper2 = new THREE.SpotLightHelper( spotLight2 );
        scene.add( pointLightHelper2 );

        spotLight3.position.set( 300, 200, 0 );
        spotLight3.target = object;
        scene.add(spotLight3);
        let pointLightHelper3 = new THREE.SpotLightHelper( spotLight3 );
        scene.add( pointLightHelper3 );

        spotLight4.position.set( -300, 200, 0 );
        spotLight4.target = object;
        scene.add(spotLight4);
        let pointLightHelper4 = new THREE.SpotLightHelper( spotLight4 );
        scene.add( pointLightHelper4 );

        //light on all objects
        const ambientLight = new THREE.AmbientLight( 0xFFFFFF, 1.5 ); // soft white light
        scene.add( ambientLight );
    }

    function animate() {
        requestAnimationFrame(animate);
        // required if controls.enableDamping or controls.autoRotate are set to true
        controls.update();
        renderer.render(scene, camera);
    }

    window.addEventListener('resize', () => {
        maxWidth = document.getElementById('canvasContainer').clientWidth;
        maxHeight = maxWidth - 100;
        renderer.setSize(maxWidth, maxHeight);
        camera.aspect = maxWidth / maxHeight;
        camera.updateProjectionMatrix();
    })
</script>

<style scoped>

</style>
