const express = require('express');
const cookieParser = require('cookie-parser');
const authRoutes = require('./routes/authRoutes');

const app = express();

// Middleware
app.use(express.json());
app.use(cookieParser());

// Rotte
app.use('/node-api', authRoutes);

// Avvio del server
app.listen(3000, () => {
    console.log('Server Node.js in ascolto su http://localhost:3000');
});
