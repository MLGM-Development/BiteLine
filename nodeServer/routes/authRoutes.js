const express = require('express')
const {login} = require('../controllers/authController')
const logout = require('../controllers/logoutController')
const authJWT = require('../middleware/authMiddleware')

const router = express.Router()

router.post('/login', login)
router.post('/logout', logout)
router.get('/protected', authJWT, (req, res) =>{
    res.json({message: 'Benvenuto ${req.user.role}', user: req.user})
})

module.exports = router