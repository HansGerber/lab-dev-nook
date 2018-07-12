<div id="canWrapper"></div>
<script src="../three.js-master/build/three.min.js"></script>
<script src="game.js"></script>
<script>

addEventListener("load", function() {
	
	game.init({
		initScene: function() {
			// wall 1
			var geometryWallOne = new THREE.BoxGeometry(0.1, 2, 6);
			var materialWallOne = new THREE.MeshBasicMaterial({ color: 0x222222 });
			this.geometricObjects.wall1 = new THREE.Mesh(
				geometryWallOne,
				materialWallOne
			)
			this.geometricObjects.wall1.position.x = -1;
			
			// wall 2
			var geometryWallTwo = new THREE.BoxGeometry(0.1, 2, 6);
			var materialWallTwo = new THREE.MeshBasicMaterial({ color: 0x222222 });
			this.geometricObjects.wall2 = new THREE.Mesh(
				geometryWallTwo,
				materialWallTwo
			)
			this.geometricObjects.wall2.position.x = 1;
			
			// floor
			var geometryFloor = new THREE.BoxGeometry(2, 0.1, 6);
			var materialFloor = new THREE.MeshBasicMaterial({ color: 0x090909 });
			this.geometricObjects.floor = new THREE.Mesh(
				geometryFloor,
				materialFloor
			)
			this.geometricObjects.floor.position.y = -1;
			
			// add objects to scene
			this.scene.add(
				this.geometricObjects.wall1,
				this.geometricObjects.wall2,
				this.geometricObjects.floor
			);
		},
		animationFrame: function() {
			
			var speed = 0.025;
			
			if(this.keysPressed.Left){
				this.camera.position.x -= speed;
			}
			
			if(this.keysPressed.Right){
				this.camera.position.x += speed;
			}
			
			if(this.keysPressed.Up){
				this.camera.position.z -= speed;
			}
			
			if(this.keysPressed.Down){
				this.camera.position.z += speed;
			}
			
			this.render()
		},
		afterInit: function() {
			this.start();
		}
	});
	
}, false);
</script>