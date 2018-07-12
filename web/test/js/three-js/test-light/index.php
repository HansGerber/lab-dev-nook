<style>
</style>

<div id="viewContainer"></div>
<script src="../three.js-master/build/three.min.js"></script>
<script>
	var screenWidth = 400
	var screenHeight = 400
	var FOV = 75
	var aspectRatio = screenWidth / screenHeight
	var renderingDistance = {
		near: 0.1,
		far: 1000
	}

	var scene = new THREE.Scene()
	
	var camera = new THREE.PerspectiveCamera(
		FOV,
		aspectRatio,
		renderingDistance.near,
		renderingDistance.far
	);
	camera.position.z = 5
	
	var renderer = new THREE.WebGLRenderer({antialias: true});
	renderer.setSize( screenWidth, screenHeight );
	
	document.getElementById("viewContainer").appendChild( renderer.domElement );
	
	var keysPressed = {
		shift:false,
		left: false,
		up: false,
		right: false,
		bottom: false,
	}
	
	addEventListener("keydown", function(e) {
		console.log(e.keyCode);
		switch(e.keyCode){
			case 16:
				keysPressed.shift = true;
			break;
			case 37:
				keysPressed.left = true;
			break;
			case 38:
				keysPressed.up = true;
			break;
			case 39:
				keysPressed.right = true;
			break;
			case 40:
				keysPressed.bottom = true;
			break;
		}
	}, false)
	
	addEventListener("keyup", function(e) {
		switch(e.keyCode){
			case 16:
				keysPressed.shift = false;
			break;
			case 37:
				keysPressed.left = false;
			break;
			case 38:
				keysPressed.up = false;
			break;
			case 39:
				keysPressed.right = false;
			break;
			case 40:
				keysPressed.bottom = false;
			break;
		}
	}, false)
	
	var geometryNormal = new THREE.BoxGeometry( 1, 1, 1 )
	var geometrySmall = new THREE.BoxGeometry( 0.5, 0.5, 0.5 )
	var materialDarkGray = new THREE.MeshPhongMaterial({
		color: 0x333333,
		//shading: THREE.FlatShading,
	});
	var materialWhite = new THREE.MeshBasicMaterial({
		color: 0xffffff,
		//shading: THREE.FlatShading,
	});
	var cube = new THREE.Mesh( geometryNormal, materialDarkGray );
	var lightCube = new THREE.Mesh( geometrySmall, materialWhite );
	cube.position.x = 1;
	cube.position.y = 1;
	lightCube.position.x = 2;
	lightCube.position.y = 2;
	lightCube.position.z = 2;
	scene.add( cube, lightCube );
	
	// create light
	
	var light = new THREE.PointLight( 0xffffff, 1, 4000 );
	light.position.set( 2, 2, 2 );
	scene.add( light );
	
	var mode = 1; // 1 = move light, 2 = move camera
	var lightMovementSpeed = 0.1;
	
	function animate(){
		requestAnimationFrame( animate )
		
		if(mode == 1){
			if(keysPressed.left){
				light.position.x -= lightMovementSpeed
				lightCube.position.x -= lightMovementSpeed
			}
			if(keysPressed.up){
				if(keysPressed.shift){
					light.position.z -= lightMovementSpeed
					lightCube.position.z -= lightMovementSpeed
				} else {
					light.position.y += lightMovementSpeed
					lightCube.position.y += lightMovementSpeed
				}
			}
			if(keysPressed.right){
				light.position.x += lightMovementSpeed
				lightCube.position.x += lightMovementSpeed
			}
			if(keysPressed.bottom){
				if(keysPressed.shift){
					light.position.z += lightMovementSpeed
					lightCube.position.z += lightMovementSpeed
				} else {
					light.position.y -= lightMovementSpeed
					lightCube.position.y -= lightMovementSpeed
				}
			}
		} else if(mode == 2){
			if(keysPressed.left){
				camera.position.x -= lightMovementSpeed
			}
			if(keysPressed.up){
				if(keysPressed.shift){
					camera.position.z -= lightMovementSpeed
				} else {
					camera.position.y += lightMovementSpeed
				}
			}
			if(keysPressed.right){
				camera.position.x += lightMovementSpeed
			}
			if(keysPressed.bottom){
				if(keysPressed.shift){
					camera.position.z += lightMovementSpeed
				} else {
					camera.position.y -= lightMovementSpeed
				}
			}
		}
		
		renderer.render( scene, camera )
	}
	
	animate()
</script>