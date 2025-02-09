//Richiamo il router
const express = require('express')
const jwt = require('jsonwebtoken')

//Richiamo dei vari moduli
const {login} = require('../controllers/authController')
const logout = require('../controllers/logoutController')
const authJWT = require('../middleware/authMiddleware')

//Istanza del router
const router = express.Router()

//Creazione delle rotte POST
router.post('/login', login)
router.post('/logout', logout)

//Rotta di verifica del cookie
router.get('/protected', (req, res) => {
    const authHeader = req.headers['authorization'];
    if (!authHeader) return res.status(401).send('Access Denied');

    const token = authHeader.split(' ')[1];
    if (!token) return res.status(401).send('Access Denied');

    try {
        const verified = jwt.verify(token, process.env.JWT_SECRET);
        res.json({ id: verified.id, role: verified.role });
    } catch (err) {
        res.status(400).send('Invalid Token');
    }
});

module.exports = router