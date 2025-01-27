document.querySelector('#lobtn').addEventListener('click', async () => {
    try {
        const response = await fetch('http://localhost:3000/node-api/logout', {
            method: 'POST',
            credentials: 'include', // Necessario per inviare i cookie
        });

        if (response.ok) {
            alert('Logout effettuato con successo');
            window.location.href = '/BiteLine/Frontend/pages/users/session/login.html';
        } else {
            alert('Errore durante il logout');
        }
    } catch (error) {
        console.error('Errore durante il logout:', error);
        alert('Errore nella comunicazione con il server.');
    }
});
