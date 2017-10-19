<!DOCTYPE html>
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
<canvas id="game" width="600" height="400"></canvas>
<div id="debug"></div>
<script>
// Websocket stuff

var ws = new WebSocket("ws://lab.dev-nook.de:6020");
//var ws = new WebSocket("ws://localhost:6020");


ws.onopen = function (e) {
	console.log("open");
	initGame();
};

ws.onerror = function (e) {
	console.log("error");
}


ws.onmessage = function(e){
	//console.log(e.data);
	
	var data = e.data.split(":");
	var command = data[0];
	var value = (data.length > 1 ? e.data.replace(command + ":", "") : "");
	
	switch(command){
		case 'update':
			var update = JSON.parse(value);
			players = update;
			drawGame();
		break;
		case 'clientId':
			me.id = value;
		break;
	}
}

// Websocket stuff - end

// Game

var players = [];
var me = {};
var spriteId = 0;
var game = document.getElementById("game");
var gameCtx = game.getContext("2d");

var playerTexture = new Image();
var meTexture = new Image();
playerTexture.src = "img/player.jpg";
meTexture.src = "img/me.jpg";

function drawEnvironment() {
	gameCtx.fillStyle = "#000";
	gameCtx.fillRect(0, 0, game.width, game.height);
}

function drawPlayers(){
	var spriteIndex = players.length;
	while(spriteIndex--){
		if(players[spriteIndex].id != me.id){
			gameCtx.drawImage(
				playerTexture,
				players[spriteIndex].x,
				players[spriteIndex].y
			);
		}
	}
	
	gameCtx.drawImage(meTexture, me.x, me.y);
}

function drawGame(){
	drawEnvironment();
	drawPlayers();
}

var me = {
	x: 0,
	y: 0,
	w: 50,
	h: 50
};



var movedLeft = false;
var movedUp = false;
var movedRight = false;
var movedDown = false;

var stepSize = 50;
var movementSpeed = 100;


function moveLeft(){
	if(movedLeft == false){
		movedLeft = true;
		me.x -= stepSize;
		drawGame();
		ws.send("move:left");
		setTimeout(function() {
			movedLeft = false;
		}, movementSpeed);
	}
}

function moveUp(){
	if(movedUp == false){
		movedUp = true;
		me.y -= stepSize;
		drawGame();
		ws.send("move:up");
		setTimeout(function() {
			movedUp = false;
		}, movementSpeed);
	}
}

function moveRight(){
	if(movedRight == false){
		movedRight = true;
		me.x += stepSize;
		drawGame();
		ws.send("move:right");
		setTimeout(function() {
			movedRight = false;
		}, movementSpeed);
	}
}

function moveDown(){
	if(movedDown == false){
		movedDown = true;
		me.y += stepSize;
		drawGame();
		ws.send("move:down");
		setTimeout(function() {
			movedDown = false;
		}, movementSpeed);
	}
}

function runBot(){
	var dir = 1;
	setInterval(function() {
		if(me.x >= game.width - me.w){
			dir = 2;
		}
		if(me.x <= 0){
			dir = 1;
		}
		
		if(dir == 1){
			moveRight();
		} else {
			moveLeft();
		}
	}, 1);
}

function initGame(){
	addEventListener("keydown", function(e){
		switch(e.keyCode){
			case 37:
				moveLeft();
			break;
			case 38:
				moveUp();
			break;
			case 39:
				moveRight();
			break;
			case 40:
				moveDown();
			break;
		}
		
	}, false);

	addEventListener("keyup", function(e){
		switch(e.keyCode){
			case 39:
				
			break;
		}
	}, false);
	
	setTimeout(function() {
		drawGame();
	}, 500);
}

// Game - end

</script>
</body>
</html>
