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
	physContactMaterials: {},
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
	addBox: function(objectConfig) {
		
		if(typeof this.objects[objectConfig.name] == "undefined"){
			if(typeof objectConfig == "undefined"){
				var objectConfig = {}
			}
			objectConfig.mass = typeof objectConfig.mass === "number" ? objectConfig.mass : 1;
			objectConfig.color = typeof objectConfig.color === "number" ? objectConfig.color : 0x00ff00;
			

			var object = {
				body: null,
				physContactMaterial: null,
				mesh: null,
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
			
			var bodyOptions = {
				mass: objectConfig.mass
			}
			if("physContactMaterial" in objectConfig){
				console.log(objectConfig.physContactMaterial, this.physContactMaterials);
				if(!(objectConfig.physContactMaterial in this.physContactMaterials)){
					this.physContactMaterials[objectConfig.physContactMaterial] = new CANNON.Material(objectConfig.physContactMaterial);
				}
				object.physContactMaterial = this.physContactMaterials[objectConfig.physContactMaterial]
				bodyOptions.material = object.physContactMaterial;
			}
			
			var body = new CANNON.Body(bodyOptions);
			
			body.addShape(shape);
			
			
			var geometry = new THREE.BoxGeometry(
				size[0] * 2,
				size[1] * 2,
				size[2] * 2
			)
			
			var materialConfig = {
				color: objectConfig.color || 0xffffff,
				wireframe: objectConfig.wireframe || false
			}
			
			if("textureURL" in objectConfig){
				var texture = new THREE.TextureLoader().load( objectConfig.textureURL );
				texture.wrapS = objectConfig.wrapS || THREE.RepeatWrapping;
				texture.wrapT = objectConfig.wrapT || THREE.RepeatWrapping;
				texture.repeat.set(
					objectConfig.repeatS || 1,
					objectConfig.repeatT || 1,
				);
				materialConfig.map = texture;
			}
			
			var material = null;
			if("materialType" in objectConfig){
				switch(objectConfig.materialType){
					case 'phong':
						material = new THREE.MeshPhongMaterial(materialConfig);
					break;
					case 'lambert':
						material = new THREE.MeshLambertMaterial(materialConfig);
					break;
					case 'basic':
					default:
						material = new THREE.MeshBasicMaterial(materialConfig);
					break;	
				}
			} else {
				material = new THREE.MeshBasicMaterial(materialConfig);
			}
			
			var mesh = new THREE.Mesh(
				geometry,
				material
			)
			
			object.body = body;
			object.mesh = mesh;
			
			object.canJump = (function() {
				var raycaster = new THREE.Raycaster();
				
				// TODO ...
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
        addModel: function(objectConfig) {
            if(typeof this.objects[objectConfig.name] == "undefined"){
                var object = {
                    body: null,
                    physContactMaterial: null,
                    mesh: null,
                }
                
                // object.mesh = new THREE.OBJLoader().load( objectConfig.modelURL );
                
                var loader = new THREE.OBJLoader();
                loader.load(
                    objectConfig.modelURL,
                    (function ( object ) {
                        console.log('obj loader success this' ,this);
                        this.gameObject.mesh = object;
                        this.game.scene.add( object );
                        if(typeof this.config.after == "function"){
                            this.config.after.call({
                                model: this.gameObject,
                            });
                        }
                    }).bind({
                        game: this,
                        config: objectConfig,
                        gameObject: object
                    }),
                    function ( xhr ) { console.log("obj loader progress"); },
                    function ( error ) { console.log("obj loader error"); }
                );
        
                return object;
            }
            return false;
        },
	remove: function(object) {
		
	},
	addContactMaterial: function(m1, m2, contactMaterialConfig){
		console.log('this.physContactMaterials', this.physContactMaterials);
		console.log(m1, m2, m2 in this.physContactMaterials);
		if(m1 in this.physContactMaterials && m2 in this.physContactMaterials){
			
			contactMaterialConfig.friction = "friction" in contactMaterialConfig ? contactMaterialConfig.friction : 0;
			contactMaterialConfig.restitution = "restitution" in contactMaterialConfig ? contactMaterialConfig.restitution : 0;
			
			var contactMaterial = new CANNON.ContactMaterial(
				this.physContactMaterials[m1],
				this.physContactMaterials[m2],
				contactMaterialConfig
			);
			console.log('contactMaterial', contactMaterial);
			
			this.world.addContactMaterial(contactMaterial)
			
			return contactMaterial;
		}
		return false;
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
