// Seleziona il form
const loginForm = document.querySelector('#loginForm');

// Aggiungi un event listener per il submit
loginForm.addEventListener('submit', async (event) => {
    event.preventDefault(); // Previene il ricaricamento della pagina

    // Raccogli i dati dal form
    const email = document.querySelector('#email').value;
    const password = document.querySelector('#password').value;
    const role = document.querySelector('#role').value

    try {
        // Invio della richiesta al backend
        const response = await fetch('http://localhost/node-api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password, role }),
        });

        const data = await response.json();

        if (response.ok) {
            // Reindirizza alla pagina admin
            window.location.href = data.redirectUrl;
        } else {
            // Errore durante il login
            alert(`Errore: ${data.error}`);
        }
    } catch (error) {
        console.error('Errore durante il login:', error);
        alert('Errore nella comunicazione con il server.');
    }
});