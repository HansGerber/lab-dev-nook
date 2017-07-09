/*
 *
 */

const WebSocket = require('ws');
const crypto = require('crypto');

const wss = new WebSocket.Server({ port: 6020 });
var clients = {}, shots = {}, i = 0;
var expPerLevel = 20;
var gameStateChanged = false, autoImcrementId = 0;

function printClients(){
	console.log("List of clients:")
	for(p in clients){
		console.log(clients[p]._player.name)
	}
}

function newPlayer(name){
	var maxLife = 6;
	return {
		'name': name,
		'posX': 0,
		'posY': 0,
		'dir': 0,
		'score': 0,
		'life': maxLife,
		'maxLife': maxLife,
		'level':1,
		'experience':0,
		'class':'warrior',
		'joinTime': '',
	}
}

function newShot(posX, posY){
	return {
		'posX': posX,
		'posY': posY,
		'distance': 0
	}
}

function removeClient(userName){
	var newClientList = {}, userExisted = false;
	for(p in clients){
		if(clients[p]._player.name != userName){
			newClientList[p] = clients[p];
		} else {
			userExisted = true;
		}
	}
	clients = newClientList;
	return userExisted;
}

function clientExists(userName){
	for(p in clients){
		if(clients[p]._player.name == userName){
			return true;
		}
	}
	return false;
}

function getUsers(){
	var users = [];
	for(p in clients){
		users.push(clients[p]._player);
	}
	return users;
}

function removeShot(index){
	var newShots = {};
	for(p in shots){
		if(p != index){
			newShots[p] = shots[p];
		}
	}
	shots = newShots;
}

function getShots(){
	var _shots = [];
	for(p in shots){
		_shots.push(shots[p]);
	}
	return _shots;
}

function broadcast(message, excludeId){
	for(p in clients){
		if(clients[p].readyState == WebSocket.OPEN){
			if(typeof excludeId == "undefined" || p != excludeId){
				clients[p].send(message);
			}
		}
	}
}

function detectShotCollision(currentShotIndex, currentPlayerId){
	for(p in clients){
		if(p != currentPlayerId){
			if(
				shots[currentShotIndex].posX >= clients[p]._player.posX - 50 &&
				shots[currentShotIndex].posX <= clients[p]._player.posX + 50 &&
				shots[currentShotIndex].posY >= clients[p]._player.posY - 50 &&
				shots[currentShotIndex].posY <= clients[p]._player.posY + 50
			){
				clients[currentPlayerId]._player.score++;
				clients[currentPlayerId]._player.experience += 1;
				if(clients[currentPlayerId]._player.experience - (clients[currentPlayerId]._player.level - 1) * expPerLevel >= expPerLevel){
					clients[currentPlayerId]._player.level++;
				}
				clients[p]._player.life--;
				sendClientUserDeath(p);
				broadcast("userList:" + JSON.stringify(getUsers()));
				return true;
			}
		}
	}
	return false;
}

function userIsDead(clientId){
	if(typeof clients[clientId] != "undefined" && clients[clientId]._player.life <= 0){
		if(clients[clientId]._player.life < 0){
			clients[clientId]._player.life = 0;
		}
		return true;
	}
	return false;
}

function sendClientUserDeath(clientId){
	if(typeof clients[clientId] != "undefined" && userIsDead(clientId)){
		clients[clientId].send("gameOver");
	}
}

wss.on('connection', function connection(ws) {
	console.log("Client connected")
	var currentWsIndex = 0;
	var shotRecovery = false;
	var shotRecoveryDuration = 500;
	var shotMaxDistance = 20;
	var shotSpeed = 40;
	var moveStepSize = 50;
	
	ws.on('message', function incoming(message) {
		//console.log('received: %s', message);
		if(message.indexOf("user:") > -1){
			console.log("user");
			var _clientExists = false;
			var name = message.replace(/user\:/, '');
			_clientExists = clientExists(name);
			if(_clientExists == false){
				currentWsIndex = ++autoImcrementId ;
				ws._id = currentWsIndex;
				ws._player = newPlayer(name);
				clients[ws._id] = ws;
				ws.send("userAdded");
				broadcast("userList:" + JSON.stringify(getUsers()));
				//printClients()
			} else {
				ws.send("userAlreadyExists")
				console.log("User exists (" + name + ")")
			}
		} else if(message == "moveLeft"){
			if(ws._id != 0 && userIsDead(ws._id) == false){
				clients[ws._id]._player.posX -= moveStepSize;
				clients[ws._id]._player.dir = 0;
				//broadcast("userList:" + JSON.stringify(getUsers()));
				gameStateChanged = true;
			}
		} else if(message == "moveUp"){
			if(ws._id != 0 && userIsDead(ws._id) == false){
				clients[ws._id]._player.posY -= moveStepSize;
				clients[ws._id]._player.dir = 1;
				//broadcast("userList:" + JSON.stringify(getUsers()));
				gameStateChanged = true;
			}
		} else if(message == "moveRight"){
			if(ws._id != 0 && userIsDead(ws._id) == false){
				clients[ws._id]._player.posX += moveStepSize;
				clients[ws._id]._player.dir = 2;
				//broadcast("userList:" + JSON.stringify(getUsers()));
				gameStateChanged = true;
			}
		} else if(message == "moveDown"){
			if(ws._id != 0 && userIsDead(ws._id) == false){
				clients[ws._id]._player.posY += moveStepSize;
				clients[ws._id]._player.dir = 3;
				//broadcast("userList:" + JSON.stringify(getUsers()));
				gameStateChanged = true;
			}
		} else if(message.indexOf("shoot:") > -1){
			if(ws._id != 0 && userIsDead(ws._id) == false){
				if(shotRecovery == false){
					var shootDir = JSON.parse(message.replace(/shoot\:/, ''));
					var diffX = shootDir[0], diffY = shootDir[1];
					var currentShotIndex = crypto.randomBytes(32).toString('hex');
					var collisionDetected = false;
					
					shotRecovery = true;
					
					if(Math.abs(diffX) > Math.abs(diffY)){
						diffY = diffY / Math.abs(diffX);
						diffX = diffX / Math.abs(diffX);
					} else if(diffX == diffY){
						diffX = diffX / Math.abs(diffX);
						diffY =  diffY / Math.abs(diffY);
					} else {
						diffX = diffX / Math.abs(diffY);
						diffY = diffY / Math.abs(diffY);
					}
					
					var shot = newShot(
						clients[ws._id]._player.posX + 10,
						clients[ws._id]._player.posY + 10
					);
						
					setTimeout(function() {
						shotRecovery = false;
					}, shotRecoveryDuration);
					
					shots[currentShotIndex] = shot;
					
					var shotTimer = setInterval(function() {
						if(typeof shots[currentShotIndex] != "undefined"){
							if(shots[currentShotIndex].distance > shotMaxDistance || collisionDetected == true){
								clearInterval(shotTimer);
								removeShot(currentShotIndex);
								broadcast('shots:{"shots":' + JSON.stringify(getShots()) + ',"users":' + JSON.stringify(getUsers()) + '}');
								return true;
							}
							shots[currentShotIndex].distance++;
							shots[currentShotIndex].posY += diffY * (25 - shots[currentShotIndex].distance);
							shots[currentShotIndex].posX += diffX * (25 - shots[currentShotIndex].distance);
							collisionDetected = detectShotCollision(currentShotIndex, ws._id);
							broadcast('shots:{"shots":' + JSON.stringify(getShots()) + ',"users":' + JSON.stringify(getUsers()) + '}');
						} else {
							return true;
						}
					}, shotSpeed);
				}
			}
		}
	});
	
	ws.on('close', function incoming(message) {
		if(ws._id != 0){
			removeClient(ws._player.name)
			console.log("Client diconnected");
			//printClients()
			broadcast("userList:" + JSON.stringify(getUsers()));
		}
	})
});

setInterval(
	function() {
		if(gameStateChanged == true){
			broadcast("userList:" + JSON.stringify(getUsers()));
			console.log("update");
			gameStateChanged = false;
		}
	},
	100
);
