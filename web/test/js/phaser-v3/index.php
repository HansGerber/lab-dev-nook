<script src="//cdn.jsdelivr.net/npm/phaser@3.16.2/dist/phaser.min.js"></script>
<script>
	var preload = function() {
		this.load.setBaseURL('http://labs.phaser.io');

        this.load.image('sky', 'assets/skies/space3.png');
        this.load.image('logo', 'assets/sprites/phaser3-logo.png');
        this.load.image('red', 'assets/particles/red.png');
	}

	var create = function() {
		this.add.image(400, 300, 'sky');

        var particles = this.add.particles('red');

        var emitter = particles.createEmitter({
            speed: 100,
            scale: { start: 1, end: 0 },
            // blendMode: 'SKIP_CHECK',
            // blendMode: 'NORMAL',
            blendMode: 'ADD',
            // blendMode: 'MULTIPLY',
            // blendMode: 'SCREEN',
            // blendMode: 'OVERLAY',
            // blendMode: 'DARKEN',
            // blendMode: 'LIGHTEN',
            // blendMode: 'COLOR_DODGE',
            // blendMode: 'COLOR_BURN',
            // blendMode: 'HARD_LIGHT',
            // blendMode: 'SOFT_LIGHT',
            // blendMode: 'DIFFERENCE',
            // blendMode: 'EXCLUSION',
            // blendMode: 'HUE',
            // blendMode: 'SATURATION',
            // blendMode: 'COLOR',
            // blendMode: 'LUMINOSITY',
            // blendMode: 'ERASE',
            // blendMode: 'SOURCE_IN',
            // blendMode: 'SOURCE_OUT',
            // blendMode: 'SOURCE_ATOP',
            // blendMode: 'DESTINATION_OVER',
            // blendMode: 'DESTINATION_IN',
            // blendMode: 'DESTINATION_OUT',
            // blendMode: 'DESTINATION_ATOP',
			// ...
        });

        var logo = this.physics.add.image(400, 100, 'logo');

        logo.setVelocity(100, 0);
        logo.setBounce(1, 1);
        logo.setCollideWorldBounds(true);

        emitter.startFollow(logo);
	}

	var config = {
		type: Phaser.AUTO,
		width: 800,
		height: 600,
		physics: {
			default: 'arcade',
			arcade: {
				gravity: { y: 200 }
			},
		},
		scene: {
			preload: preload,
			create: create,
		},
	}

	var game = new Phaser.Game(config)

	

</script>
