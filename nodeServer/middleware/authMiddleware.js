const jwt = require('jsonwebtoken') //Libreria dei cookie
require('dotenv').config()

const secretKey = process.env.JWT_SECRET

const authJWT = (req, res, next) => {

    //Si prende il token dai cookie dal browser
    const token = req.cookies.auth_token

    
    if(!token) {
        //Se il token non è stato creato allora da errore
        return res.status(401).json({
            message: 'Accesso negato, token mancante' 
        })

        try {
            const decoded = jwt.verify(token, secretKey)
            req.user = decoded
            next()
        } catch (e){
            return res.status(403).json({redirectUrl: '/BiteLine/Frontend/pages/session/login.html', error: "Token non valido"})
        }
    }
}

module.exports = authJWT