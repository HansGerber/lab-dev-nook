<!DOCTYPE html>
<html>
<head>
<style>
body, html {
	overflow:hidden;
}
body {
	padding:0;
	margin:0;
}
</style>
</head>
<body>
<canvas id="game" width="1000" height="1000"></canvas>
<div id="debug"></div>
<script>
//var wsUrl = "ws://localhost:6020";
var wsUrl = "ws://lab.dev-nook.de:6020";
var ws = new WebSocket(wsUrl);
var appSendUpdateTimer = -1;
var appSendUpdateSpeed = 50;
var players = [];
var shots = [];

var game = document.getElementById("game");
var gameCtx = game.getContext("2d");
var playerStepWidth = 30;
var playerMovement = {
	moveLeftTimer:-1,
	moveUpTimer:-1,
	moveRightTimer:-1,
	moveDownTimer:-1
};
var playerSize = {
	w:40,
	h:40
};
var shotSize = {
	w:20,
	h:20
};

var mPos = [0, 0];
var mId = -1;
var keys = {
	left:false,
	up:false,
	right:false,
	down:false,
};
var keysPostData = {
	left:false,
	up:false,
	right:false,
	down:false,
};
var mouseClick = {
	x:0,
	y:0,
}
var mouseClickPostData = {
	x:0,
	y:0,
}
var keepKeyState = false;
var keyIsDown = false;

function drawPlayers(){
	if(players.length > 0){
		for(n in players){
			if(mId != players[n].id){
				gameCtx.fillStyle = "#f00";
				gameCtx.fillRect(
					players[n].posX - playerSize.w / 2,
					players[n].posY - playerSize.h / 2,
					playerSize.w,
					playerSize.h
				);
			} else {
				gameCtx.fillStyle = "#0f0";
				gameCtx.fillRect(
					mPos[0] - playerSize.w / 2,
					mPos[1] - playerSize.h / 2,
					playerSize.w,
					playerSize.h
				);
			}
		}
	}
}

function drawShots(){
	if(shots.length > 0){
		for(n in shots){			
			gameCtx.fillStyle = "#ff0";
			gameCtx.fillRect(
				shots[n].x - shotSize.w / 2,
				shots[n].y - shotSize.h / 2,
				shotSize.w,
				shotSize.h
			);
		}
	}
}

function drawBackground(){
	gameCtx.fillStyle = "#300";
	gameCtx.fillRect(
		0,
		0,
		game.width,
		game.height
	);
}

function drawGame(){
	drawBackground();
	drawPlayers();
	drawShots();
}

function startAppDataUpdater() {
	if(ws){
		appSendUpdateTimer = setInterval(function() {
			keepKeyState = false;
			if(ws.bufferedAmount == 0){
				var postData = {}
				if(keysPostData.left != false || keysPostData.up != false || keysPostData.right != false || keysPostData.down != false){
					postData.keys = keysPostData;
				}
				if(mouseClickPostData.x != 0 || mouseClickPostData.y != 0){
					postData.click = mouseClickPostData;
				}
				ws.send("controls:" + JSON.stringify(postData));
				syncPostKeys();
				syncPostClick();
			}
		}, appSendUpdateSpeed);
	}
}

function stopAppDataUpdater(){
	clearInterval(appSendUpdateTimer);
	appSendUpdateTimer = -1;
}
function syncPostKeys(){
	keysPostData.left = keys.left;
	keysPostData.up = keys.up;
	keysPostData.right = keys.right;
	keysPostData.down = keys.down;
}
function syncPostClick(){
	mouseClickPostData.x = mouseClick.x;
	mouseClickPostData.y = mouseClick.y;
}

function stopMovementTimers(){
	for(timer in playerMovement){
		if(playerMovement[timer] != -1){
			clearInterval(playerMovement[timer]);
			playerMovement[timer] = -1;
		}
	}
}

function initMessageHandler(){
	if(ws){
		ws.onmessage = function(e){
			console.log(e.data);
			
			var data = e.data.split(":");
			var command = data[0];
			var value = (data.length > 1 ? e.data.replace(command + ":", "") : "");
			
			switch(command){
				case 'gameData':
					var gameData = JSON.parse(value);
					players = gameData.players;
					shots = gameData.shots;
					if(mId > -1){
						for(n in players){
							if(players[n].id == mId){
								mPos[0] = players[n].posX;
								mPos[1] = players[n].posY;
							}
						}
					}
					
					drawGame();
				break;
				case 'yourId':
					mId = value;
				break;
				case 'error':
					endApp();
					switch(value){
						case 'playerLimitReached':
							alert("There are already 2 players in game ... please wait and retry a bit later :)")
						break;
						default:
						
						break;
					}
				break;
			}
		}
	}
}

function keyDown(e){
	switch(e.keyCode){
		case 37:
			keys.left = true;
		break;
		case 38:
			keys.up = true;
		break;
		case 39:
			keys.right = true;
		break;
		case 40:
			keys.down = true;
		break;
	}
	syncPostKeys();
}

function keyUp(e){
	switch(e.keyCode){
		case 37:
			keys.left = false;
		break;
		case 38:
			keys.up = false;
		break;
		case 39:
			keys.right = false;
		break;
		case 40:
			keys.down = false;
		break;
	}
}

function initControlHandler(){
	addEventListener("keydown", function(e) {
		e.preventDefault();
		keyDown(e);
	}, false);
	addEventListener("keyup", function(e) {
		keyUp(e);
	}, false);
	
	addEventListener("mousedown", function(e) {
		mouseClick.x = e.clientX;
		mouseClick.y = e.clientY;
		syncPostClick();
	}, false);
	
	addEventListener("mousedown", function(e) {
		mouseClick.x = 0;
		mouseClick.y = 0;
	});
}

function startApp(e){
	if(ws){
		initMessageHandler();
		
		initControlHandler();
		
		ws.send("playerJoin:John");
		
		startAppDataUpdater();
	}
}

function endApp(){
	stopAppDataUpdater();
}

// TEST
function startBot(){
	var dir = 1;
	setInterval(function() {
		if(mPos[0] >= 500){
			keyUp({
				keyCode: 39
			});
			dir = 2;
		} else if(mPos[0] <= 0){
			keyUp({
				keyCode: 37
			});
			dir = 1;
		}
		if(dir == 1){
			keyDown({
				keyCode: 39
			})
		} else {
			keyDown({
				keyCode: 37
			})
		}
	}, 1);
}
// TEST - end

ws.onopen = function (e) {
	startApp(e);
};

ws.onerror = function (e) {
	alert("Connection failed");
}

var debugDiv = document.getElementById("debug");

//TEST


</script>
</body>
</html>