//Richiamo il router
const express = require('express')

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
router.get('/protected', authJWT, (req, res) =>{
    res.json({message: 'Benvenuto ${req.user.role}', user: req.user})
})

module.exports = router