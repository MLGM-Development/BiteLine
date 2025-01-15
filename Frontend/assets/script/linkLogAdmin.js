// Seleziona il form
const loginForm = document.getElementById('loginForm');

// Aggiungi un event listener per il submit
loginForm.addEventListener('submit', async (event) => {
    event.preventDefault(); // Previene il ricaricamento della pagina

    // Raccogli i dati dal form
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        // Invio della richiesta al backend
        const response = await fetch('http://localhost/node-api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        });

        const data = await response.json();

        if (response.ok) {
            // Login riuscito
            alert('Login effettuato con successo!');
            // Reindirizza alla pagina admin
            window.location.href = '/BiteLine/Frontend/pages/admins/adminControls/adminPage.php';
        } else {
            // Errore durante il login
            alert(`Errore: ${data.error}`);
        }
    } catch (error) {
        console.error('Errore durante il login:', error);
        alert('Errore nella comunicazione con il server.');
    }
});
