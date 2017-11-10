const os = require("os");
const WebSocket = require('ws');
const fs = require('fs');

const wss = new WebSocket.Server({ port: 6020 });

// server functions

var clients = [], autoIncrementClientId = 0, i = 0, updateSpeed = 50, hasUpdates = false, logFileURL = "log.txt";

function writeLog(message){
	var date = new Date();
	fs.appendFile(logFileURL,
	"[" +
	date.getFullYear() + "-" +
	date.getMonth() + " " +
	date.getHours() + ":" +
	date.getMinutes() + ":" +
	date.getSeconds() +
	"] " +
	message + os.EOL, function (err) {
		if (err) throw err;
	});
}

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

var gameAreaDimensions = [1000, 600], gameArea = [], shots = [], shotsPostData = [];

var maxPlayers = 4;
var playerStepWidth = 20, playerMaxHP = 100;
var playerSize = {
	w: 40,
	h: 40
}

var shotStepWidth = 30, shotMaxDisance = 10, shotDamage = 5;
var shotSize = {
	w: 20,
	h: 20
}

function initGameArea(){
	for(var y = 0; y < gameAreaDimensions[1].length; y++){
		var line = [];
		for(var x = 0; x < gameAreaDimensions[0].length; x++){
			line.push(Math.round(Math.random()));
		}
		gameArea.push(line);
	}
}

function getRandomStartPos(){
	return {
		x:Math.round(Math.random() * gameAreaDimensions[0]),
		y:Math.round(Math.random() * gameAreaDimensions[1])
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
			if(clients[wsIndex].player.x >= playerStepWidth){
				clients[wsIndex].player.x -= playerStepWidth;
			}
		} else if(keys.right == true){
			if(clients[wsIndex].player.x <= gameAreaDimensions[0] - playerStepWidth){
				clients[wsIndex].player.x += playerStepWidth;
			}
		}
		if(keys.up == true){
			if(clients[wsIndex].player.y >= playerStepWidth){
				clients[wsIndex].player.y -= playerStepWidth;
			}
		} else if(keys.down == true){
			if(clients[wsIndex].player.y <= gameAreaDimensions[1] - playerStepWidth){
				clients[wsIndex].player.y += playerStepWidth;
			}
		}
		return true;
	}
	return false;
}

function fireShot(pId, playerPos, clickPos){
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
			pId:pId,
			x:playerPos.x,
			y:playerPos.y,
			sX:diffX,
			sY:diffY,
			d:0 // distance
		});
	}
}

function processShots() {
	if(shots.length > 0){
		var result = [];
		for(n in shots){
			if(shots[n].d < shotMaxDisance){
				shots[n].d++;
				shots[n].x += shotStepWidth * shots[n].sX;
				shots[n].y += shotStepWidth * shots[n].sY;
				if(handleShotCollision(shots[n]) == false){
					result.push(shots[n]);
				}
			}
			hasUpdates = true;
		}
		shots = 0;
		shots = result;
	}
}

function handleShotCollision(shot){
	var shotsAfterCollision = [], originalShooterIndex = getPlayerWSIndexById(shot.pId);
	for(c in clients){
		if(
			clients[c].player.id != shot.pId &&
			clients[c].player.d == false
		){
			if(
				Math.abs(clients[c].player.x - shot.x) < playerSize.w / 2 + shotSize.w / 2 && 
				Math.abs(clients[c].player.y - shot.y) < playerSize.h / 2 + shotSize.h / 2
			){
				if(clients[c].player.hp > 0){
					clients[c].player.hp -= shotDamage;
					if(clients[c].player.hp <= 0){
						clients[c].player.hp = 0;
						clients[c].player.d = true;
						clients[originalShooterIndex].player.k++;
					}
				}
				return true;
			}
		}
	}
	return false;
}

function getPlayerWSIndexById(id){
	var wsCounter = clients.length;
	
	while(wsCounter--){
		if(clients[wsCounter].player.id == id){
			return wsCounter;
		}
	}
	
	return -1;
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
	
	if(maxPlayers > clients.length){
		ws.id = getNextClientAutoIncrementId();
		addClient(ws);
		writeLog("(connect) " + clients.length + " clients");
		
		ws.on('message', function incoming(_message) {
			//console.log('received: %s', _message);
			
			var data = _message.split(":");
			var command = data[0];
			var value = (data.length > 1 ? _message.replace(command + ":", "") : "")
			
			switch(command){
				case 'playerJoin':
					var startPos = getRandomStartPos();
					ws.player = {
						'id': ws.id,
						'n': value, // name
						'x': startPos.x,
						'y': startPos.y,
						'hp':playerMaxHP,
						'k':0, // kills
						'd':false // dead
					}
					ws.send("yourId:" + ws.player.id);
					ws.send("arena:" + JSON.stringify(gameArea));
					hasUpdates = true;
				break;
				case 'controls':
					if(ws.player.hp > 0){
						var controls = JSON.parse(value);
						if(typeof controls.keys != "undefined"){
							setPlayerPos(ws.id, controls.keys);
							hasUpdates = true;
						}
						if(typeof controls.click != "undefined"){
							fireShot(
								ws.player.id,
								{
									x:ws.player.x,
									y:ws.player.y
								},
								{
									x:controls.click.x,
									y:controls.click.y
								}
							);
							hasUpdates = true;
						}
					}
					
				break;
			}
		});
		
		ws.on('close', function incoming(_message) {
			hasUpdates = true;
			removeClient(ws.id);
			writeLog("(discconnect) " + clients.length + " clients");
			console.log("client disconnected");
		})
	} else {
		ws.send("error:playerLimitReached");
	}
});

// Update process
setInterval(function() {
	processShots();
	
	if(hasUpdates){
		var postData = {
			players:getPlayers(),
			shots:shots
		}
		broadcast("gameData:" + JSON.stringify(postData));
		hasUpdates = false;
	}
}, updateSpeed);
