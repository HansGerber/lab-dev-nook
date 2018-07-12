<html>
	<head>
		<style>
			body {
				padding:0;
				margin:0;
			}
		</style>
	</head>
	<body>
		<script src="../../three.js-master/build/three.min.js"></script>
		<script src="../../cannon.js/build/cannon.min.js"></script>
		<script>
			// example by Schteppe:
			// https://github.com/schteppe/cannon.js/blob/master/examples/threejs.html
		
			var world, mass, body1, body2, shape1, shape2, timeStep=1/60,
			 camera, scene, renderer, geometry1, material1, mesh1, geometry2, material2, mesh2, pointLight, ambientLight;
			 
			  initThree();
			  initCannon();
			  animate();
			  
			  function initCannon() {
				  world = new CANNON.World();
				  world.gravity.set(0, -10, 0);
				  world.broadphase = new CANNON.NaiveBroadphase();
				  world.solver.iterations = 10;
				  
				  // falling box
				  shape = new CANNON.Box(new CANNON.Vec3(1, 1, 1));
				  body1 = new CANNON.Body({
					mass: 1
				  });
				  body1.addShape(shape);
				  body1.position.y = 10;
				  body1.angularVelocity.set(0, 1, 1);
				  body1.angularDamping = 0.5;
				  
				  // box to catch the fall of falling box
				  shape2 = new CANNON.Box(new CANNON.Vec3(1, 1, 1));
				  body2 = new CANNON.Body({
					mass: 0
				  });
				  body2.addShape(shape);
				  body2.position.y = -10;
				  body2.sleep(); // set immovable (unaffected by gravity)
				  
				  world.addBody(body1);
				  world.addBody(body2);
			  }
			  
			  function initThree() {
				  scene = new THREE.Scene();
				  camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 100 );
				  camera.position.z = 20;
				  camera.position.x = 5;
				  scene.add( camera );
				  
				  geometry1 = new THREE.BoxGeometry( 2, 2, 2 );
				  material1 = new THREE.MeshPhongMaterial( { color: 0xff0000, wireframe: false } );
				  mesh1 = new THREE.Mesh( geometry1, material1 );
				  scene.add( mesh1 );
				  
				  geometry2 = new THREE.BoxGeometry( 2, 2, 2 );
				  material2 = new THREE.MeshPhongMaterial( { color: 0x00ff00, wireframe: false } );
				  mesh2 = new THREE.Mesh( geometry2, material2 );
				  scene.add( mesh2 );
				  
				  renderer = new THREE.WebGLRenderer();
				  renderer.setSize( window.innerWidth, window.innerHeight );
				  document.body.appendChild( renderer.domElement );
				  
				  ambientLight = new THREE.AmbientLight(0xffffff, 0.5)
				  pointLight = new THREE.PointLight(0xffffff, 1, 1000)
				  pointLight.position.set( 10, 50, 50 )
				  scene.add( pointLight, ambientLight );
			  }
			  
			  function animate() {
				  requestAnimationFrame( animate );
				  updatePhysics();
				  render();
			  }
			  
			  function updatePhysics() {
				  // Step the physics world
				  world.step(timeStep);
				  
				  // Copy coordinates from Cannon.js to Three.js
				  mesh1.position.copy(body1.position);
				  mesh1.quaternion.copy(body1.quaternion);
				  mesh2.position.copy(body2.position);
				  mesh2.quaternion.copy(body2.quaternion);
			  }
			  
			  function render() {
				  renderer.render( scene, camera );
			  }
		</script>
	</body>
</html>