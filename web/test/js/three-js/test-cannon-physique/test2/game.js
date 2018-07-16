var game = window.game || {
	config: {
		width: 600,
		height: 400,
		phys: {
			timeStep: 1/60,
			gravity: [0, -10, 0]
		},
		camera: {
			fov: 45,
			aspectRatio: 600 / 400,
			nearRender: 0.1,
			farRender: 1000
		}
	},
	objects: {},
	world: null,
	renderer: null,
	scene: null,
	camera: null,
	input: {
		keysPressed: {
			SPACE: false,
			LEFT: false,
			UP: false,
			RIGHT: false,
			DOWN: false
		},
		mouse: {
			pos: {
				x: 0,
				y: 0
			}
		}
	},
	initialized: false,
	init: function(config) {
		addEventListener("keydown", (function(e){
			switch(e.keyCode){
				case 32:
					this.input.keysPressed.SPACE = true;
				break;
				case 37:
					this.input.keysPressed.LEFT = true;
				break;
				case 38:
					this.input.keysPressed.UP = true;
				break;
				case 39:
					this.input.keysPressed.RIGHT = true;
				break;
				case 40:
					this.input.keysPressed.DOWN = true;
				break;
				default:
			}
		}).bind(this), false);

		addEventListener("keyup", (function(e){
			switch(e.keyCode){
				case 32:
					this.input.keysPressed.SPACE = false;
				break;
				case 37:
					this.input.keysPressed.LEFT = false;
				break;
				case 38:
					this.input.keysPressed.UP = false;
				break;
				case 39:
					this.input.keysPressed.RIGHT = false;
				break;
				case 40:
					this.input.keysPressed.DOWN = false;
				break;
				default:
			}
		}).bind(this), false);
		
		this.world = new CANNON.World();
		this.world.broadphase = new CANNON.NaiveBroadphase();
		this.world.solver.iterations = 10;
		this.world.gravity.set(
			this.config.phys.gravity[0],
			this.config.phys.gravity[1],
			this.config.phys.gravity[2]
		)
		
		this.renderer = new THREE.WebGLRenderer();
		this.scene = new THREE.Scene();
		this.camera = new THREE.PerspectiveCamera(
			this.config.camera.fov,
			this.config.camera.aspectRatio,
			this.config.camera.nearRender,
			this.config.camera.farRender
		)

		this.renderer.setSize(
			this.config.width,
			this.config.height
		)
		
		document.body.appendChild(this.renderer.domElement);
		this.scene.add(this.camera);
		
		this.render();
		
		this.initialized = true;
		
		if("renderLogic" in config && typeof config.renderLogic == "function"){
			this.renderLogic = config.renderLogic.bind(this);
		}
		
		if("after" in config && typeof config.after == "function"){
			config.after.bind(this)();
		}
	},
	addObject: function(objectConfig) {
		
		if(typeof this.objects[objectConfig.name] == "undefined"){
			if(typeof objectConfig == "undefined"){
				var objectConfig = {}
			}
			objectConfig.mass = typeof objectConfig.mass === "number" ? objectConfig.mass : 1;
			objectConfig.color = typeof objectConfig.color === "number" ? objectConfig.color : 0x00ff00;
			

			var object = {
				body: null,
				mesh: null
			}

			var size = [
				objectConfig.size[0] || 1,
				objectConfig.size[1] || 1,
				objectConfig.size[2] || 1
			];
			
			var shape = new CANNON.Box(
				new CANNON.Vec3(
					size[0],
					size[1],
					size[2]
				)
			);
			var body = new CANNON.Body({
				mass: objectConfig.mass,
			});
			body.addShape(shape);
			
			
			var geometry = new THREE.BoxGeometry(
				size[0] * 2,
				size[1] * 2,
				size[2] * 2
			)
			var material = new THREE.MeshBasicMaterial({
				color: objectConfig.color || 0x00ff00,
				wireframe: objectConfig.wireframe || false
			})
			var mesh = new THREE.Mesh(
				geometry,
				material
			)
			
			object.body = body;
			object.mesh = mesh;
			
			object.canJump = (function() {
				var raycaster = new THREE.Raycaster();
			}).bind({
				object: object,
				game: this
			})
			
			this.world.addBody(body);
			this.scene.add(mesh);
			
			this.objects[objectConfig.name] = object;
			
			return object
		}
		return false;
	},
	removeObject: function(object) {
		
	},
	on: function(eventName, fnCallback){
		if(typeof fnCallback == "function"){
			var handler = fnCallback.bind(this);
			switch(eventName){
				default:
					addEventListener(eventName, handler, false);
			}
			return handler;
		}
		return false;
	},
	updatePhysics: function() {
		this.world.step(this.config.phys.timeStep);
		for(p in this.objects){
			this.objects[p].mesh.position.copy(this.objects[p].body.position);
			this.objects[p].mesh.quaternion.copy(this.objects[p].body.quaternion);
		}
	},
	renderLogic: null,
	render: function() {
		requestAnimationFrame(this.render.bind(this))
		
		if(typeof this.renderLogic == "function"){
			(this.renderLogic.bind(this))();
		}
		
		this.updatePhysics();
		this.renderer.render(
			this.scene,
			this.camera
		)
	}
}
