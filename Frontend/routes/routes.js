function loadCss(cssPath) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = cssPath;
    document.head.appendChild(link);
}

function loadComponents(selector, filepath, cssPath = null){
    fetch(filepath).then(response => {
        if(!response.ok){
            throw new Error(`Errore nel caricamento del ${filepath}`);
        }
        return response.text();
    }).then(content => {
        document.querySelector(selector).innerHTML = content;

        if (cssPath){
            loadCss(cssPath);
        }

    }).catch(error => console.error(error));
}

document.addEventListener('DOMContentLoaded', () => {
    loadComponents("#header", "/Frontend/components/Header/header.html", "/Frontend/assets/css/styleComponent/header.css");
    loadComponents("#footer", "/Frontend/components/Footer/footer.html", "/Frontend/assets/css/styleComponent/footer.css");
});