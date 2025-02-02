document.querySelector('#lobtn').addEventListener('click', async () => {
    try {
        const response = await fetch('http://localhost:3000/node-api/logout', { //Fetch della route
            method: 'POST',
            credentials: 'include', // Necessario per cancellare i cookie
        });

        //se il fetch va a buon fine allora lo reindirizza al login
        if (response.ok) {
            alert('Logout effettuato con successo');
            window.location.href = '/BiteLine/Frontend/pages/users/session/login.html';
        } else {
            alert('Errore durante il logout'); //Errore
        }
    } catch (error) { //Catch dell'eccezione
        console.error('Errore durante il logout:', error);
        alert('Errore nella comunicazione con il server.');
    }
});
