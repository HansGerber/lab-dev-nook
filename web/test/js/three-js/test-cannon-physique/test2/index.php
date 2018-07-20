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
                
		function lockCameraToObject(object){
			this.camera.position.x = object.body.position.x;
			this.camera.position.y = object.body.position.y + 20;
			this.camera.position.z = object.body.position.z + 20;
			this.camera.rotation.x = -(Math.PI / 4);
		}
		
		addEventListener("load", function() {
			
			game.init({
				after:function() {
					
					this.world.addEventListener("postStep", function() {
						// console.log("postStep", game.world.contacts);
					}, false);
					
					fallingObj = this.addObject({
						name: 'fallling',
						physContactMaterial: 'freeObject',
						wireframe: true,
						size: [2, 1, 2],
					});
					
					groundObj1 = this.addObject({
						name: 'ground1',
						physContactMaterial: 'solidImmovable',
						mass: 0,
						color: 0xff0000,
						wireframe: true,
						size: [1, 1, 1],
					});
					
					groundObj2 = this.addObject({
						name: 'ground2',
						physContactMaterial: 'solidImmovable',
						mass: 0,
						color: 0x0000ff,
						wireframe: true,
						size: [6, 0.5, 6],
					});
					
					fallingObj.body.position.y = 10;
					fallingObj.body.fixedRotation = true;
					fallingObj.body.updateMassProperties();
					fallingObj.body.addEventListener("collide", function(e) {
						// console.log("object collided", e);
					}, false);
					
					this.on("keydown", function(e) {
						if(e.keyCode == 32){
							jump();
						}
					});
					
					groundObj1.body.position.x = 1;
					groundObj1.body.position.y = 5;
					
					groundObj2.body.position.y = -4;
					
					this.addContactMaterial('freeObject', 'solidImmovable', {});
				},
				renderLogic: function() {
					fallingObj.body.velocity.x = 0;
					fallingObj.body.velocity.z = 0;
					this.input.keysPressed.LEFT ? fallingObj.body.velocity.x = -5 : false;
					this.input.keysPressed.UP ? fallingObj.body.velocity.z = -5 : false;
					this.input.keysPressed.RIGHT ? fallingObj.body.velocity.x = 5 : false;
					this.input.keysPressed.DOWN ? fallingObj.body.velocity.z = 5 : false;
					
					(lockCameraToObject.bind(this, fallingObj))();
				},
			});
			
		}, false)
		</script>
	</body>
</html>