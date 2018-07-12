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
		
		
		addEventListener("load", function() {
			
			game.init({
				after:function() {
					
					this.camera.position.z = 20;
					
					var fallingObj = this.addObject({
						name: 'fallling',
						wireframe: true,
						size: [2, 1, 2],
					});
					
					var groundObj1 = this.addObject({
						name: 'ground1',
						mass: 0,
						color: 0xff0000,
						wireframe: true,
						size: [1, 1, 1],
					});
					
					var groundObj2 = this.addObject({
						name: 'ground2',
						mass: 0,
						color: 0x0000ff,
						wireframe: true,
						size: [4, 0.5, 4],
					});
					
					fallingObj.body.position.y = 10;
					
					groundObj1.body.position.x = 1;
					groundObj1.body.position.y = 5;
					
					groundObj2.body.position.y = -1;
				}
			});
			
		}, false)
		</script>
	</body>
</html>