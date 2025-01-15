const express = require('express');
const app = express();

// Esempio di endpoint Node.js
app.get('/node-api/test', (req, res) => {
    res.json({ message: 'Ciao dal server Node.js!' });
});

// Porta in ascolto
app.listen(3000, () => {
    console.log('Server Node.js in ascolto sulla porta 3000');
});
