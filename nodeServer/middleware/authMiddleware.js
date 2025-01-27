const jwt = require('jsonwebtoken')
require('dotenv').config()
const secretKey = process.env.JWT_SECRET

const authJWT = (req, res, next) => {
    const token = req.cookies.auth_token

    if(!token) {
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