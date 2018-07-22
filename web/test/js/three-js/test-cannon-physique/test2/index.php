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
		
		var freeObject,
			groundObj, groundObj2,
			wallObject1, wallObject2, wallObject3, wallObject4, wallObject5,
			flashLight;
		
		function jump(){
			// TODO: prevent when not on ground
			freeObject.body.velocity.y = 5;
		}
                
		function lockCameraToObject(object){
			this.camera.position.x = object.body.position.x;
			this.camera.position.y = object.body.position.y + 20;
			this.camera.position.z = object.body.position.z + 20;
			this.camera.rotation.x = -(Math.PI / 4);
		}
		
		function lockFlashLightToObject(object){
			flashLight.position.set(
				object.body.position.x,
				object.body.position.y,
				object.body.position.z
			);
		}
		
		addEventListener("load", function() {
			
			game.init({
				after:function() {
					
					this.world.addEventListener("postStep", function() {
						// console.log("postStep", game.world.contacts);
					}, false);
					
					// MOVABLE OBJECT
					//
					
					freeObject = this.addObject({
						name: 'freeObject',
						physContactMaterial: 'freeObject',
						mass: 4,
						//wireframe: true,
						size: [2, 3, 2],
					});
					
					freeObject.body.position.y = 4;
					freeObject.body.fixedRotation = true;
					freeObject.body.updateMassProperties();
					freeObject.body.addEventListener("collide", function(e) {
						// console.log("object collided", e);
					}, false);
					
					this.on("keydown", function(e) {
						if(e.keyCode == 32){
							jump();
						}
					});
					
					// GROUND OBJECTS
					//
					
					groundObj = this.addObject({
						name: 'ground1',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/silent_hill_floor_1_by_felhek.jpg',
						repeatT: 4,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [6, 0.5, 24],
					});
					
					groundObj2 = this.addObject({
						name: 'ground2',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/silent_hill_floor_1_by_felhek.jpg',
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [6, 0.5, 18],
					});
					
					groundObj2.body.position.x = 12;
					groundObj2.body.position.z = 6;
					
					// WALL OBJECTS
					//
					
					wallObject1 = this.addObject({
						name: 'wall1',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/debris_by_darkwood67.jpg',
						repeatS: 3,
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [1, 12, 18],
					});
					
					wallObject2 = this.addObject({
						name: 'wall2',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/debris_by_darkwood67.jpg',
						repeatS: 1,
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [6, 12, 1],
					});
					
					wallObject3 = this.addObject({
						name: 'wall3',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/debris_by_darkwood67.jpg',
						repeatS: 1,
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [1, 12, 6],
					});
					
					wallObject4 = this.addObject({
						name: 'wall4',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/debris_by_darkwood67.jpg',
						repeatS: 1,
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [6, 12, 1],
					});
					
					wallObject5 = this.addObject({
						name: 'wall5',
						physContactMaterial: 'solidImmovable',
						textureURL: 'img/debris_by_darkwood67.jpg',
						repeatS: 1,
						repeatT: 3,
						mass: 0,
						materialType: 'phong',
						color: 0xffffff,
						//wireframe: true,
						size: [1, 12, 6],
					});
					
					wallObject1.body.position.x = -6;
					wallObject1.body.position.y = 12;
					wallObject1.body.position.z = 6;
					
					wallObject2.body.position.y = 12;
					wallObject2.body.position.z = -13;
					
					wallObject3.body.position.x = 6;
					wallObject3.body.position.y = 12;
					wallObject3.body.position.z = -6;
					
					wallObject4.body.position.x = 13;
					wallObject4.body.position.y = 12;
					wallObject4.body.position.z = -1;
					
					wallObject5.body.position.x = 18;
					wallObject5.body.position.y = 12;
					wallObject5.body.position.z = 6;
					
					// CONTACT BEHAVIOUR
					//
					
					this.addContactMaterial('freeObject', 'solidImmovable', {});
					
					// FLASH LIGHT
					//
					
					flashLight = new THREE.PointLight(0xffffff, 2, 10);
					this.scene.add( flashLight );
				},
				renderLogic: function() {
					var walkSpeed = 5;
					
					freeObject.body.velocity.x = 0;
					freeObject.body.velocity.z = 0;
					this.input.keysPressed.LEFT ? freeObject.body.velocity.x = -walkSpeed : false;
					this.input.keysPressed.UP ? freeObject.body.velocity.z = -walkSpeed : false;
					this.input.keysPressed.RIGHT ? freeObject.body.velocity.x = walkSpeed : false;
					this.input.keysPressed.DOWN ? freeObject.body.velocity.z = walkSpeed : false;
					
					(lockCameraToObject.bind(this))(freeObject);
					lockFlashLightToObject(freeObject);
				},
			});
			
		}, false)
		</script>
	</body>
</html>