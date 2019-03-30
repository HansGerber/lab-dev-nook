<div id="canvasWrapper"></div>
<script>
	var scene, camera, renderer,
		screenWidth = 600, screenHeight = 400,
		canvasWrapper = document.getElementById("canvasWrapper"),
		light, gltfMesh;
		
	function render(){
		requestAnimationFrame( render );
		
		if(gltfMesh){
			gltfMesh.rotation.y += 0.1;
		}
		
		renderer.render( scene, camera );
	}
	
	addEventListener("load", function() {
		
		scene = new THREE.Scene();
		
		camera = new THREE.PerspectiveCamera( 75,  screenWidth / screenHeight, 0.1, 1000 );
		camera.position.z = 3;
		
		renderer = new THREE.WebGLRenderer();
		renderer.setSize( screenWidth, screenHeight );
		renderer.setClearColor( 0xffffff );
		canvasWrapper.appendChild( renderer.domElement );
		
		// MODEL
		var loader = new THREE.GLTFLoader();
		
		// Optional: Provide a DRACOLoader instance to decode compressed mesh data
		/* THREE.DRACOLoader.setDecoderPath( '/examples/js/libs/draco' );
		loader.setDRACOLoader( new THREE.DRACOLoader() ); */
		
		loader.load(
			'test.gltf',
			function ( gltf ) {
				gltfMesh = gltf.scene;
				scene.add( gltf.scene );
			},
			function ( xhr ) {
				console.log( ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
			},
			function ( error ) {
				console.log( 'An error happened' );
			}
		);
		
		// LIGHTING
		light = new THREE.PointLight(
			0xffffff,
			1,
			15
		)
		light.position.x = 10
		light.position.y = 10
		scene.add( light );
		
		// RENDER
		
		render();
	}, false);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/93/three.min.js"></script>
<script src="gltf-loader.js"></script>
<script src="draco-loader.js"></script>