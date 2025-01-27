const logout = (res, req) =>{
    try{
        req.session.destroy((err) => {
            if(err){
                console.error("Errore durante il lgout: ", err)
                return res.status(500).json({message: "Errore durante il logout"})
            }

            res.clearCookie('connect.sid');
            return res.status(200).json({message: "Logout effettuato con successo"})
        })
    }catch(e){
        console.error('Errore: ', e)
        return res.status(500).json({message: 'Errire interno del server'})
    }
}

module.exports = logout