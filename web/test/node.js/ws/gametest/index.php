<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Press+Start+2P" rel="stylesheet">
<style>
body, html {
	overflow:hidden;
	height:100%;
}
body {
	padding:0;
	margin:0;
	background:#555 url(images/alien-bg.jpg) no-repeat;
	background-size:cover;
	font:100%/1.5 arial;
}
#ajaxLoader {
	position:absolute;
	top:0;
	left:0;
	right:0;
	bottom:0;
}
#ajaxLoader img {
	margin-top:20px;
	margin:0 auto;
	display:inline-block;
}
#highscoreList {
	position:absolute;
	background:rgba(100, 250, 0, 0.2);
	width:150px;
	padding:10px;
	box-sizing:border-box;
	color:rgb(100, 250, 0);
	border-bottom-left-radius: 10px;
	font-family:'Press Start 2P', cursive;
	font-size: 10px;
    white-space: nowrap;
    line-height: 2;
}
</style>
</head>
<body>
	<div id="highscoreList"></div>
	<div id="page">
		<canvas id="game"></canvas>
		<div id="debug"></div>
	</div>
	<div id="ajaxLoader">
		<img src="images/ajax-loader.gif">
	</div>
<script>
//var wsUrl = "ws://localhost:6020";
var wsUrl = "ws://lab.dev-nook.de:6020";
var ws = new WebSocket(wsUrl);
var appSendUpdateTimer = -1;
var appSendUpdateSpeed = 50;
var hasUpdates = false;
var doHighscoreUpdate = 20;

var highscoreList = document.getElementById("highscoreList");
var game = document.getElementById("game");
var gameCtx = game.getContext("2d");
var ajaxLoader = document.getElementById("ajaxLoader");
var page = document.getElementById("page");
var gameAreaDimensions = [1000, 600];

var spriteObjects = [
	{name:'ufo_you',path:'images/ufo_you.png'},
	{name:'ufo_enemy',path:'images/ufo_enemy.png'},
	{name:'dead_player',path:'images/dead.png'},
	{name:'bg_space',path:'images/space-bg.jpg'}
];

var sprites = {
	playerYou:null,
	playerEnemy:null,
	deadPlayer:null,
	spaceBg:null,
}

var players = [];
var shots = [];

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
var playerMaxHP = 100;
var playerStepWidth = 20;
var healthBarWidth = playerMaxHP, healthBarHeight = 5;
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
var keyIsDown = false;

function sortPlayersByHighscore(a, b){
	return b.k - a.k;
}

function updateHighscoreList(players){
	highscoreList.innerHTML = "";
	var sortedPlayers = players.sort(sortPlayersByHighscore);
	for(var i = 0; i < 5; i++){
		if(typeof players[i] != "undefined"){
			highscoreList.innerHTML += "Player#" + players[i].id + " : " + players[i].k + "<br>";
		} else {
			highscoreList.innerHTML += "-<br>";
		}
	}
}

function drawBackground(){
	gameCtx.fillStyle = "#000";
	gameCtx.fillRect(
		0,
		0,
		game.width,
		game.height
	);
	gameCtx.drawImage(
		sprites.spaceBg.imgObj,
		game.width - 500,
		game.height - 500
	);
}

function drawPlayerHealthBar(playerData){
	gameCtx.fillStyle = "#222";
	gameCtx.fillRect(
		playerData.x - healthBarWidth / 2,
		playerData.y - playerSize.h / 2 - 20,
		healthBarWidth,
		healthBarHeight
	);
	gameCtx.fillStyle = "#0f0";
	gameCtx.fillRect(
		playerData.x - healthBarWidth / 2,
		playerData.y - playerSize.h / 2 - 20,
		playerData.hp,
		healthBarHeight
	);
}

function drawPlayers(){
	if(players.length > 0){
		for(n in players){
			if(players[n].d == false){
				if(mId != players[n].id){
					gameCtx.drawImage(
						sprites.playerEnemy.imgObj,
						players[n].x - playerSize.w / 2,
						players[n].y - playerSize.h / 2
					);
				} else {
					gameCtx.drawImage(
						sprites.playerYou.imgObj,
						players[n].x - playerSize.w / 2,
						players[n].y - playerSize.h / 2
					);
				}
			} else {
				gameCtx.drawImage(
					sprites.deadPlayer.imgObj,
					players[n].x - playerSize.w / 2,
					players[n].y - playerSize.h / 2
				);
			}
			drawPlayerHealthBar(players[n]);
		}
	}
}

function drawShots(){
	if(shots.length > 0){
		for(n in shots){			
			gameCtx.fillStyle = "#ff0";
			
			gameCtx.beginPath();
			gameCtx.arc(
				shots[n].x - shotSize.w / 2,
				shots[n].y - shotSize.h / 2,
				shotSize.w / 2,
				0,
				2*Math.PI
			);
			gameCtx.fill();
		}
	}
}

function drawGame(){
	drawBackground();
	drawPlayers();
	drawShots();
}

function startAppDataUpdater() {
	if(ws){
		var _i = 0;
		appSendUpdateTimer = setInterval(function() {
			document.title = ++_i;
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
				interpolateMovePlayer(keys);
			}
			
			if(doHighscoreUpdate == 1){
				doHighscoreUpdate = 20;
				
				updateHighscoreList(players);
			}
			doHighscoreUpdate--;
			
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

function interpolateMovePlayer(keys){
	if(keys.left == true){
		if(mPos[0] >= playerStepWidth){
			mPos[0] -= playerStepWidth;
		}
	} else if(keys.right == true){
		if(mPos[0] <= gameAreaDimensions[0] - playerStepWidth){
			mPos[0] += playerStepWidth;
		}
	}
	if(keys.up == true){
		if(mPos[1] >= playerStepWidth){
			mPos[1] -= playerStepWidth;
		}
	} else if(keys.down == true){
		if(mPos[1] <= gameAreaDimensions[1] - playerStepWidth){
			mPos[1] += playerStepWidth;
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
								mPos[0] = players[n].x;
								mPos[1] = players[n].y;
							}
						}
					}
					
					drawGame();
				break;
				case 'yourId':
					mId = value;
				break;
				case 'error':
					alert("error");
					switch(value){
						case 'playerLimitReached':
							alert("There are already 4 players in game ... please wait and retry a bit later :)");
						break;
						default:
							alert("something went wrong :(");
						break;
					}
					endApp();
				break;
			}
		}
	}
}

function keyDown(e){
	switch(e.keyCode){
		case 37:
		case 65:
			keys.left = true;
		break;
		case 38:
		case 87:
			keys.up = true;
		break;
		case 39:
		case 68:
			keys.right = true;
		break;
		case 40:
		case 83:
			keys.down = true;
		break;
	}
	syncPostKeys();
}

function keyUp(e){
	switch(e.keyCode){
		case 37:
		case 65:
			keys.left = false;
		break;
		case 38:
		case 87:
			keys.up = false;
		break;
		case 39:
		case 68:
			keys.right = false;
		break;
		case 40:
		case 83:
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
		e.preventDefault();
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
	ajaxLoader.style.display = "none";
	page.style.display = "block";
	
	game.width = gameAreaDimensions[0];
	game.height = gameAreaDimensions[1];
	
	highscoreList.style.left = (game.width - 150) + "px";
	
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
function bot(){
	var dir = 1;
	setInterval(function() {
		if(mPos[0] >= 400){
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

function preloadSprites(callback){
	var imageLoadCount = 0, imageLoadFinalCount = spriteObjects.length;
	if(imageLoadFinalCount > 0){
		for(let n in spriteObjects){
			spriteObjects[n].imgObj = new Image();
			spriteObjects[n].imgObj.onload = function(){
				imageLoadCount++;
				if(imageLoadCount >= imageLoadFinalCount){
					if(typeof callback == "function"){
						callback();
					}
				}
			};
			spriteObjects[n].imgObj.src = spriteObjects[n].path;
		}
	} else {
		if(typeof callback == "function"){
			callback();
		}
	}
}

function getSprite(name){
	for(n in spriteObjects){
		if(spriteObjects[n].name == name){
			return spriteObjects[n];
		}
	}
}

ws.onopen = function (e) {
	preloadSprites(function() {
		
		sprites.playerYou = getSprite("ufo_you");
		sprites.playerEnemy = getSprite("ufo_enemy");
		sprites.deadPlayer = getSprite("dead_player");
		sprites.spaceBg =  getSprite("bg_space");
		
		startApp(e);
	});
};

ws.onerror = function (e) {
	alert("Connection failed");
}

</script>
</body>
</html>
