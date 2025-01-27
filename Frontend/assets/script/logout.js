const logout = document.querySelector('#lobtn')

logout.addEventListener('click', async () =>{
    try {
        const response = await fetch('http://localhost:3000/node-api/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
        })

        const data = await response.json();

        if(response.ok){
            alert(data.message)

            window.location.href = '/BiteLine/Frontend/pages/users/session/login.html'
        } else {
            alert('Errore durante il login')
        }
    } catch (e) {
        console.error(e)
        alert('Errore nella comunicazione con il server')
    }
})