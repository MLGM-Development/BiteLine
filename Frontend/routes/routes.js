// Funzione per caricare i CSS dell'header e footer
function loadCss(cssPath) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = cssPath;
    document.head.appendChild(link);
}

//Funzione per caricare i componenti header e footer
function loadComponents(selector, filepath, cssPath = null){
    fetch(filepath).then(response => { //Qua prende il percorso del file html
        if(!response.ok){
            throw new Error(`Errore nel caricamento del ${filepath}`); //Gestione degli errori se il file non viene caricato
        }
        return response.text();
    }).then(content => {
        document.querySelector(selector).innerHTML = content;

        if (cssPath){
            loadCss(cssPath); //Caricamento del CSS
        }

    }).catch(error => console.error(error)); //Gestione degli errori
}

document.addEventListener('DOMContentLoaded', () => { //Quando il documento è pronto
    loadComponents("#header", "/Frontend/components/Header/header.html", "/Frontend/assets/css/styleComponent/header.css");
    loadComponents("#footer", "/Frontend/components/Footer/footer.html", "/Frontend/assets/css/styleComponent/footer.css");
});