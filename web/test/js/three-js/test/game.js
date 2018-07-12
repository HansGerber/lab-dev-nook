var game = {
	width: 600,
	height: 400,
	canWrapper: document.getElementById("canWrapper"),
	scene: null,
	camera: null,
	renderer: null,
	loader: null,
	fov: 90,
	ratio: 1,
	nearClippingPlane: 0.1,
	farClippingPlane: 1000,
	animationFrame: null,
	initialized: false,
	stopped: false,
	lightObjects: {},
	geometricObjects: {},
	keysPressed: {
		Left: false,
		Up: false,
		Right: false,
		Down: false,
	},
	init: function(args) {
		if(this.initialized === false){
			if(typeof THREE !== "undefined"){
				this.ratio = this.width / this.height;
				this.scene = new THREE.Scene();
				this.camera = new THREE.PerspectiveCamera(
					this.fov,
					this.ratio,
					this.nearClippingPlane,
					this.farClippingPlane
				);
				this.camera.position.z = 5;
				this.renderer = new THREE.WebGLRenderer({
					antialias: true
				});
				this.renderer.setSize(
					this.width,
					this.height
				);
				
				addEventListener("keydown", (function(e){
					switch(e.keyCode){
						case 37:
							this.keysPressed.Left = true;
						break;
						case 38:
							this.keysPressed.Up = true;
						break;
						case 39:
							this.keysPressed.Right = true;
						break;
						case 40:
							this.keysPressed.Down = true;
						break;
					}
				}).bind(this), false);
				
				addEventListener("keyup", (function(e){
					switch(e.keyCode){
						case 37:
							this.keysPressed.Left = false;
						break;
						case 38:
							this.keysPressed.Up = false;
						break;
						case 39:
							this.keysPressed.Right = false;
						break;
						case 40:
							this.keysPressed.Down = false;
						break;
					}
				}).bind(this), false);
				
				args.initScene.bind(this)()
				this.animationFrame = args.animationFrame.bind(this);
				this.canWrapper.appendChild( this.renderer.domElement );
				this.initialized = true;
				args.afterInit.bind(this)();
			}
		}
	},
	render: function() {
		this.renderer.render(
			this.scene,
			this.camera
		)
	},
	animate: function() {
		if(this.initialized && this.stopped === false){
			requestAnimationFrame( this.animate.bind(this) )
			this.animationFrame()
		}
	},
	start: function() {
		this.animate()
	}
}