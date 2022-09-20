var express = require('express');
var socket = require('socket.io');
var app = express();

var server = app.listen(4000, function () {

console.log('listening to port 4000');
});

var io = socket(server);

io.sockets.on('connection', newConnection);


function newConnection(socket){
	console.log('connected to'+ socket.id);
	
	socket.on('request_form', RequestedFormEvent);
	
	function RequestedFormEvent(data){
		socket.broadcast.emit('request_form', data);
		
	}
	
	
	
}
