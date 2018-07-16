<html>
	<head>
		<title></title>
		<style>
			body {
				padding: 0;
				margin: 0;
			}
		</style>
	</head>
	<body>
		<script src="../../three.js-master/build/three.min.js"></script>
		<script src="../../cannon.js/build/cannon.min.js"></script>
		<script src="game.js"></script>
		<script>
		
		var fallingObj, groundObj1, groundObj2, ground
			moveSpeed = 0.1;
		
		function jump(){
			// TODO: prevent when not on ground
			fallingObj.body.velocity.y = 10;
		}
		
		function moveForward(){
			/*fallingObj.body.velocity.z = 0;
			fallingObj.body.position.z -= moveSpeed;*/
			fallingObj.body.velocity.z = -5;
		}
		
		function moveRight(){
			/*fallingObj.body.velocity.x = 0;
			fallingObj.body.position.x += moveSpeed;*/
			fallingObj.body.velocity.x = 5;
		}
		
		function moveBackward(){
			/*fallingObj.body.velocity.z = 0;
			fallingObj.body.position.z += moveSpeed;*/
			fallingObj.body.velocity.z = 5;
		}
		
		function moveLeft(){
			/*fallingObj.body.velocity.x = 0;
			fallingObj.body.position.x -= moveSpeed;*/
			fallingObj.body.velocity.x = -5;
		}
		function lockCameraToObject(object){
			this.camera.position.x = object.body.position.x;
			this.camera.position.y = object.body.position.y + 20;
			this.camera.position.z = object.body.position.z + 20;
			this.camera.rotation.x = -(Math.PI / 4);
		}
		
		addEventListener("load", function() {
			
			game.init({
				after:function() {
					
					fallingObj = this.addObject({
						name: 'fallling',
						materialType: 'phong',
						size: [1, 1, 1],
					});
					
					groundObj1 = this.addObject({
						name: 'ground1',
						materialType: 'phong',
						mass: 0,
						color: 0xff0000,
						size: [2, 0.5, 2],
					});
										
					groundObj2 = this.addObject({
						name: 'ground2',
						materialType: 'phong',
						mass: 0,
						color: 0x0000ff,
						size: [6, 0.5, 6],
					});
										
					var alight = new THREE.AmbientLight(0xffffff, 0.5);
					var plight = new THREE.PointLight(0xffffff, 1, 100);
					plight.position.set(10, 30, 10)
					this.scene.add(
						plight, alight
					)

					fallingObj.body.position.y = 10;
					fallingObj.body.fixedRotation = true;
					fallingObj.body.updateMassProperties();
					groundObj1.mesh.castShadow = true;
					fallingObj.body.addEventListener("collide", function(e) {
						// console.log("object collided", e);
					}, false);
					
					groundObj1.body.position.x = 1;
					groundObj1.body.position.y = 5;
					
					groundObj2.body.position.y = -4;
					
					this.on("keydown", function(e) {
						if(e.keyCode == 32){
							jump();
						}
					});
				},
				renderLogic: function() {
					if(this.input.keysPressed.LEFT){
						moveLeft();
					}
					if(this.input.keysPressed.UP){
						moveForward();
					}
					if(this.input.keysPressed.RIGHT){
						moveRight();
					}
					if(this.input.keysPressed.DOWN){
						moveBackward();
					}
					
					(lockCameraToObject.bind(this, fallingObj))();
				},
			});
			
		}, false)
		</script>
	</body>
</html>