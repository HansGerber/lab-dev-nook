const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 6020 });

// server functions

var clients = [], autoIncrementClientId = 0, i = 0, updateSpeed = 50;

function getNextClientAutoIncrementId(){
	return ++autoIncrementClientId;
}

function addClient(client){
	clients.push(client);
}

function removeClient(id){
	var newClientList = [];
	for(i = 0; i < clients.length; i++){
		if(clients[i].id != id){
			newClientList.push(clients[i]);
		}
	}
	clients = newClientList;
}

function broadcast(message){
	for(i = 0; i < clients.length; i++){
		if(clients[i].readyState == WebSocket.OPEN){
			clients[i].send(message);
		}
	}
}

// game functions

var gameAreaDimensions = [5000, 5000], shots = [], shotsPostData = [];

var maxPlayers = 4;
var playerStepWidth = 10, playerStartHP = 100;
var playerSize = {
	w: 40,
	h: 40
}

var shotStepWidth = 20, shotMaxDisance = 10, shotDamage = 10;
var shotSize = {
	w: 20,
	h: 20
}

function initGameArea(){
	for(var y = 0; y < gameAreaDimensions.length; y++){
		for(var y = 0; y < gameAreaDimensions.length; y++){
			
		}
	}
}

function setPlayerPos(wsId, keys){
	var wsIndex = -1;
	var wsCount = clients.length;
	
	while(wsCount--){
		if(clients[wsCount].id == wsId){
			wsIndex = wsCount;
		}
	}
	
	if(wsIndex != -1){
		if(keys.left == true){
			if(clients[wsIndex].player.posX >= playerStepWidth){
				clients[wsIndex].player.posX -= playerStepWidth;
			}
		} else if(keys.right == true){
			if(clients[wsIndex].player.posX <= gameAreaDimensions[0] - playerStepWidth){
				clients[wsIndex].player.posX += playerStepWidth;
			}
		}
		if(keys.up == true){
			if(clients[wsIndex].player.posY >= playerStepWidth){
				clients[wsIndex].player.posY -= playerStepWidth;
			}
		} else if(keys.down == true){
			if(clients[wsIndex].player.posY <= gameAreaDimensions[0] - playerStepWidth){
				clients[wsIndex].player.posY += playerStepWidth;
			}
		}
		return true;
	}
	return false;
}

function fireShot(playerId, playerPos, clickPos){
	var diffX = clickPos.x - playerPos.x, diffY = clickPos.y - playerPos.y;
	
	if(Math.abs(diffX) > Math.abs(diffY)){
		if(diffX != 0){
			diffY = diffY / Math.abs(diffX);
			diffX = diffX / Math.abs(diffX);
		} else {
			if(diffY != 0){
				diffY = diffY / Math.abs(diffY);
			}
		}
	} else if(Math.abs(diffX) <= Math.abs(diffY)){
		if(diffY != 0){
			diffX = diffX / Math.abs(diffY);
			diffY = diffY / Math.abs(diffY);
		} else {
			if(diffX != 0){
				diffX = diffX / Math.abs(diffX);
			}
		}
	}
	
	if(diffX != 0 || diffY != 0){
		shots.push({
			playerId:playerId,
			x:playerPos.x,
			y:playerPos.y,
			stepX:diffX,
			stepY:diffY,
			distance:0
		});
	}
}

function updateAndReturnShots() {
	var result = [];
	for(n in shots){
		if(shots[n].distance < shotMaxDisance){
			shots[n].distance++;
			shots[n].x += shotStepWidth * shots[n].stepX;
			shots[n].y += shotStepWidth * shots[n].stepY;
			if(handleShotCollision(shots[n]) == false){
				result.push(shots[n]);
			}
		}
	}
	shots = 0;
	shots = result;
}

function handleShotCollision(shot){
	var shotsAfterCollision = [];
	for(c in clients){
		if(clients[c].player.id != shot.playerId){
			if(
				Math.abs(clients[c].player.posX - shot.x) < playerSize.w / 2 + shotSize.w / 2 && 
				Math.abs(clients[c].player.posY - shot.y) < playerSize.h / 2 + shotSize.h / 2
			){
				if(clients[c].player.hp > 0){
					clients[c].player.hp -= shotDamage;
				}
				return true;
			}
		}
	}
	return false;
}

function getPlayers(){
	var players = [];
	var wsCounter = clients.length;
	
	while(wsCounter--){
		players.push(clients[wsCounter].player);
	}
	
	return players;
}

// message handling

wss.on('connection', function connection(ws) {
	console.log("Client connected")
	
	if(maxPlayers > clients.length){
		ws.id = getNextClientAutoIncrementId();
		addClient(ws);
		
		ws.on('message', function incoming(_message) {
			//console.log('received: %s', _message);
			
			var data = _message.split(":");
			var command = data[0];
			var value = (data.length > 1 ? _message.replace(command + ":", "") : "")
			
			switch(command){
				case 'playerJoin':
					ws.player = {
						'id': ws.id,
						'name': value,
						'posX': 0,
						'posY': 0,
						'hp':playerStartHP
					}
					ws.send("yourId:" + ws.player.id);
				break;
				case 'controls':
					
					var controls = JSON.parse(value);
					if(typeof controls.keys != "undefined"){
						setPlayerPos(ws.id, controls.keys);
					}
					if(typeof controls.click != "undefined"){
						fireShot(
							ws.player.id,
							{
								x:ws.player.posX,
								y:ws.player.posY
							},
							{
								x:controls.click.x,
								y:controls.click.y
							}
						);
					}
				break;
			}
		});
		
		ws.on('close', function incoming(_message) {
			console.log("client disconnected");
			removeClient(ws.id);
		})
	} else {
		ws.send("error:playerLimitReached");
	}
});

// Update process
setInterval(function() {
	updateAndReturnShots();
	
	var postData = {
		players:getPlayers(),
		shots:shots
	}
	broadcast("gameData:" + JSON.stringify(postData));
}, updateSpeed);