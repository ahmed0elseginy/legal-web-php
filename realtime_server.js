/**
 * MIZAN Legal Services - Real-time Notification Server (D2)
 * Technology: Node.js (Express & Socket.io)
 * Purpose: This service handles real-time alerts for lawyers when new cases are assigned.
 */

const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

const PORT = 3000;

app.get('/', (req, res) => {
    res.send('Mizan Real-time Service is running...');
});

// Event: Lawyer connects
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Join a room based on lawyer ID
    socket.on('join_lawyer_room', (lawyerId) => {
        socket.join(`lawyer_${lawyerId}`);
        console.log(`Lawyer ${lawyerId} joined their notification room.`);
    });

    // Simulate receiving a new case from the PHP backend via Webhook or API
    socket.on('new_case_alert', (data) => {
        // Broadcast to the specific lawyer room
        io.to(`lawyer_${data.lawyerId}`).emit('notify', {
            message: `قضية جديدة: ${data.caseTitle}`,
            timestamp: new Date()
        });
    });

    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

server.listen(PORT, () => {
    console.log(`Node.js Real-time server listening on port ${PORT}`);
    console.log(`To run this: 
    1. Install Node.js
    2. run: npm install express socket.io
    3. run: node realtime_server.js`);
});
