const logout = (req, res) =>{
    try{
        res.clearCookie('auth_token', {path: '/'});  // 'auth_token' è il nome del cookie che usi per memorizzare il JWT

        // Risposta al client per confermare il logout
        return res.json({ message: 'Logout effettuato con successo' });
    }catch(e){
        console.error('Errore: ', e)
        return res.status(500).json({message: 'Errire interno del server'})
    }
}

module.exports = {logout}