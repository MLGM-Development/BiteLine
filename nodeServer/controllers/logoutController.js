const logout = (req, res) => {
    try {
        res.clearCookie('auth_token', {
            httpOnly: true,
            secure: false, // Usa secure solo se hai HTTPS
            sameSite: 'Strict', // Aggiunge sicurezza contro CSRF
        });
        return res.json({ message: 'Logout effettuato con successo' });
    } catch (error) {
        console.error('Errore durante il logout:', error);
        return res.status(500).json({ message: 'Errore interno del server' });
    }
};

module.exports = logout;
