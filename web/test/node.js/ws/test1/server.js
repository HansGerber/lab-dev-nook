// server variables

const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 6020 });

var clients = [], autoIncrementClientId = 0, i = 0;

// game variables

var serverUpdateSpeed = 100;
var stepWidth = 50;

// server functions

function getNextClientAutoIncrementId(){
	return ++autoIncrementClientId;
}

function addClient(client){
	clients.push(client);
}

function getClient(id){
	var clientIndex = clients.length;
	while(clientIndex--){
		if(clients[clientIndex].id == id){
			return clients[clientIndex];
		}
	}
	return null;
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

function getPlayers(){
	var clientIndex = clients.length;
	var playersArray = [];
	while(clientIndex--){
		playersArray.push(clients[clientIndex].player);
	}
	return playersArray;
}

// message handling

wss.on('connection', function connection(ws) {
	console.log("Client connected")
	
	ws.id = getNextClientAutoIncrementId();
	ws.player = {
		id: ws.id,
		x: 0,
		y: 0,
		w: 50,
		h: 50
	}
	addClient(ws);
	ws.send("clientId:" + ws.id);
	
	ws.on('message', function incoming(_message) {
		console.log('received (' + ws.id + '): ' + _message);
		
		var data = _message.split(":");
		var command = data[0];
		var value = (data.length > 1 ? _message.replace(command + ":", "") : "")
		
		switch(command){
			case 'ping':
				broadcast("client " + ws.id + " connected");
			break;
			case 'move':
				switch(value){
					case 'left':
						ws.player.x -= stepWidth;
					break;
					case 'up':
						ws.player.y -= stepWidth;
					break;
					case 'right':
						ws.player.x += stepWidth;
					break;
					case 'down':
						ws.player.y += stepWidth;
					break;
				}
			break;
		}
	});
	
	ws.on('close', function incoming(_message) {
		console.log("client " + ws.id + " disconnected");
		removeClient(ws.id)
		broadcast("client " + ws.id + " disconnected");
	})
});

// send updates regularily

setInterval(function() {
	broadcast("update:" + JSON.stringify(getPlayers()));
}, serverUpdateSpeed);