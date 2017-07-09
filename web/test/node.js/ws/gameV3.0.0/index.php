<head>
<link rel="stylesheet" href="css/main.css" />
</head>
<body oncontextmenu="return false;">
<div id="shots"></div>
<div id="game"></div>
<div id="listOfPlayers"></div>
<div id="debug"></div>
<div id="gameOverOverlay">
	<h1 id="gameOverHeader">
		Game Over
	</h1>
	<p>
		If you want to play again please reload the page :)
	</p>
</div>
<script>
var gameDiv = document.getElementById("game");
var debugDiv = document.getElementById("debug");
var listOfPlayersDiv = document.getElementById("listOfPlayers");
var shotsDiv = document.getElementById("shots");
var gameOverOverlayDiv = document.getElementById("gameOverOverlay");
var name = "";
var userList = [];
var myUser = {};
<?php if($_SERVER["HTTP_HOST"] == "lab.dev-nook.de"){ ?>
var wsUrl = "ws://lab.dev-nook.de";
<?php } else if($_SERVER["HTTP_HOST"] == "localhost") { ?>
var wsUrl = "ws://localhost";
<?php } ?>
var wsPort = 6020;
var ws;

function genRandomName(){
	name = ["John", "Pete", "Paul", "Mary", "Rick", "Jane", "Ben", "Brittany", "Kate", "Jimmy"][parseInt(Math.random() * 9.99)];
}

function moveLeft(){
	if(movingLeft == false){
		movingLeft = true;
		var moveLeftTimer = setInterval(
			function() {
				if(movingLeft == false){
					clearInterval(moveLeftTimer);
					return true;
				}
				if(myUser.life > 0){
					myUser.posX -= 1;
					document.getElementById("me").style.left = myUser.posX + "px";
					ws.send("moveLeft");
				}
			}, 1
		);
	}
}

function moveUp(){
	if(movingUp == false){
		movingUp = true;
		var moveUpTimer = setInterval(
			function() {
				if(movingUp == false){
					clearInterval(moveUpTimer);
					return true;
				}
				if(myUser.life > 0){
					myUser.posY -= 1;
					document.getElementById("me").style.top = myUser.posY + "px";
					ws.send("moveUp");
				}
			}, 1
		);
	}
}

function moveRight(){
	if(movingRight == false){
		movingRight = true;
		var moveRightTimer = setInterval(
			function() {
				if(movingRight == false){
					clearInterval(moveRightTimer);
					return true;
				}
				if(myUser.life > 0){
					myUser.posX += 1;
					document.getElementById("me").style.left = myUser.posX + "px";
					ws.send("moveRight");
				}
			}, 1
		);
	}
}

function moveDown(){
	if(movingDown == false){
		movingDown = true;
		var moveDownTimer = setInterval(
			function() {
				if(movingDown == false){
					clearInterval(moveDownTimer);
					return true;
				}
				if(myUser.life > 0){
					myUser.posY += 1;
					document.getElementById("me").style.top = myUser.posY + "px";
					ws.send("moveDown");
				}
			}, 1
		);
	}
}

function updatePlayers(userList){
	var player;
	var lifePercent = 0;
	gameDiv.innerHTML = "";
	for(i = 0; i < userList.length; i++){
		player = document.createElement("div");
		player.id = "player_" + userList[i].name;
		if(userList[i].name == name){
			player.id = "me";
			myUser = userList[i];
		}
		if(userList[i].life == 0){
			player.setAttribute("class", "deadPlayer");
			player.setAttribute("style", "left:" + userList[i].posX + "px;top:" + userList[i].posY + "px;background-image:url(images/deadHero.png);");
		} else {
			lifePercent = parseInt(userList[i].life / userList[i].maxLife * 100);
			if(lifePercent < 0){
				lifePercent = 0;
			}
			player.setAttribute("class", "player");
			player.setAttribute("style", "left:" + userList[i].posX + "px;top:" + userList[i].posY + "px;background-image:url(images/heroes/" + userList[i].name + ");");			
			player.innerHTML = '<div class="userName">' + userList[i].name + '</div>';
			player.innerHTML += '<div class="userHealthBar"><div class="userHealthBarValue" style="width:' + parseInt(userList[i].life / userList[i].maxLife * 100) + '%;"></div></div>';
			player.innerHTML += '<div class="userLevel" style="background:#0' + parseInt((userList[i].experience - (userList[i].level - 1) * 20) / 2.1) + '0;">' + userList[i].level + '</div>';
		}
		gameDiv.appendChild(player);
	}
}

function updatePlayerList(userList){
	var i = 0;
	listOfPlayersDiv.innerHTML = "";
	for(i = 0; i < userList.length; i++){
		if(userList[i].name == name){
			if(userList[i].life > 0){
				listOfPlayersDiv.innerHTML += '<span style="color:#6c6">' + userList[i].name + ' (Treffer ' + userList[i].score + ')</span><br>';
			} else {
				listOfPlayersDiv.innerHTML += '<span style="color:#6c6"><img src="images/dead.png"> ' + userList[i].name + ' (Treffer ' + userList[i].score + ')</span><br>';
			}
		} else {
			if(userList[i].life > 0){
				listOfPlayersDiv.innerHTML += '<span style="color:#c63">' + userList[i].name + ' (Treffer ' + userList[i].score + ')</span><br>';
			} else {
				listOfPlayersDiv.innerHTML += '<span style="color:#c63"><img src="images/dead.png"> ' + userList[i].name + ' (Treffer ' + userList[i].score + ')</span><br>';
			}
		}
	}
}

function handleShot(shots){
	var i = 0;
	shotsDiv.innerHTML = "";
	for(i = 0; i < shots.length; i++){
		if(shots[i].distance < 20){
			shotsDiv.innerHTML += '<div class="shot" style="left:' + shots[i].posX + 'px;top:' + shots[i].posY + 'px;"></div>';
		}
	}
}

ws = new WebSocket(wsUrl + ":" + wsPort);

ws.onopen = function (e) {
	genRandomName();
	ws.send("user:"+name);
};

ws.onmessage = function(e){
	if(e.data == "gameOver"){
		gameOverOverlayDiv.style.display = "block";
	}
		
	if(e.data.indexOf("userList:") > -1){
		userList = JSON.parse(e.data.replace(/userList\:/, ''));
		updatePlayers(userList);
		updatePlayerList(userList);
	}
	
	if(e.data == "userAlreadyExists"){
		genRandomName();
		ws.send("user:"+name);
	}
	
	if(e.data.indexOf("shots:") > -1){
		var shotData = JSON.parse(e.data.replace(/shots\:/, ''));
		debugDiv.innerHTML = JSON.stringify(shotData.shots);
		handleShot(shotData.shots);
		updatePlayers(shotData.users);
		updatePlayerList(shotData.users);
	}
}

var movingLeft = false,
	movingUp = false
	movingRight = false
	movingDown = false;

addEventListener("keydown", function(e) {
	e.preventDefault();
	switch(e.keyCode){
		case 13:
		
		break;
		case 32:
		
		break;
		case 37:
		case 65:
			moveLeft();
		break;
		case 38:
		case 87:
			moveUp();
		break;
		case 39:
		case 68:
			moveRight();
		break;
		case 40:
		case 83:
			moveDown();
		break;
	}
}, false);

addEventListener("keyup", function(e) {
	switch(e.keyCode){
		case 37:
		case 65:
			movingLeft = false;
		break;
		case 38:
		case 87:
			movingUp = false;
		break;
		case 39:
		case 68:
			movingRight = false;
		break;
		case 40:
		case 83:
			movingDown = false;
		break;
	}
}, false);

function getMousePos(e){
	return [e.clientX, e.clientY];
}

addEventListener("mousedown", function(e) {
	e.preventDefault();
	if(e.which == 1){
		// left mouse button
		var mousePos = getMousePos(e);
		var myPlayer = document.getElementById("me");
		var myPlayerPos = [
			(myPlayer.style.left.replace("px", '') * 1) + 25,
			(myPlayer.style.top.replace("px", '') * 1) + 25
		];
		
		var diffX = mousePos[0] - myPlayerPos[0];
		var diffY = mousePos[1] - myPlayerPos[1];
		
		ws.send("shoot:[" + diffX + "," + diffY + "]");
	} else if(e.which == 3){
		// right mouse button
	}
}, false);

addEventListener("mouseclick", function(e) {
	e.preventDefault();
}, false);
</script>
</body>
