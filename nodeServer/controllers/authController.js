const bcrypt = require('bcrypt') //Importo la libreria per il decrypt/crypt della psw
const crypto = require('crypto') //Importo la libreria per il cookie
const connectDatabase = require('../config/dbConfig') //Importo la connessione al DB

//Funsione per il login
const login = async (req, res) => {
    const {email, password, role} = req.body //Prendo i valori degli input del body

    if(!email || !password || !role) {
        //se l'utente non ha inserito nulla allora avrà un messaggio d'errore http (verifica backend)
        return res.status(400).json({error: 'Email, password e/o Ruolo sono richiesti'})
    }

    try {
        //se ha inserito tutto allora si procede con la connessione al database
        const connection = await connectDatabase;
        const tableName = role === 'admin' ? 'admins':'owners';
        const idFiled = role === 'admin' ? 'admin':'owner';

        const queryRetriever = `
            SELECT ${idFiled}_id AS id, email, password
            FROM ${tableName}
            WHERE email = ?
        `;

        const [rows] = await connection.execute(queryRetriever, [email])

        //si confronta il database se la mail non l'ha trovata allora da errore
        if(rows.length === 0) {
            return res.status(404).json({ error: 'Email non trovata'})
        }

        //se l'ha trovato prende il primo record dell'output, essendo solo uno
        const user = rows[0]

        //Controllo della password
        let compatibleHash = user.password.replace('$2y$', '$2a$')
        const isPasswordCorrect = await bcrypt.compare(password, compatibleHash)
        if (!isPasswordCorrect) {
            return res.status(401).json({error: 'Password errata'})
        }

        //Creazione del cookie
        const sessionId = crypto.randomBytes(32).toString('hex')
        const expiry = Math.floor(Date.now() / 1000) + 2592000 //Imposto la scadenza

        //Mi connetto al db e updato gli admin con cookie
        await connection.execute(
            `UPDATE ${role}s SET session_id = ?, cookie_expiry = ? WHERE ${role}_id = ?`,
            [sessionId, expiry, user.id]
        )

        //Creo il cookie di sessione per il browser
        res.cookie('auth_token', sessionId, {
            httpOnly: true,
            maxAge: 259000000,
            path: '/'
        })

        const redirectUrl =
            role === 'admin'
            ? '/BiteLine/Frontend/pages/admins/adminControls/adminPage.php'
            : '/BiteLine/Frontend/pages/users/Owners/ownersControls/ownerPage.php'

        res.json({
            message: `Login effettuato con successo come ${role}`,
            redirectUrl: redirectUrl
        })
    }catch (e){
        //Catch degli errori interni del server
        console.error(e)
        res.status(500).json({error: 'Errore interno del server'})
    }
}

//Esportazione del modulo
module.exports = {login}