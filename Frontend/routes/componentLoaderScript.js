function loadCss(cssPath) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = cssPath;
    document.head.appendChild(link);
}

function loadJs(jsPath) {
    const script = document.createElement('script');
    script.src = jsPath;
    script.async = true; // Carica lo script in modo asincrono
    document.body.appendChild(script);
}

// Funzione per caricare i componenti header e footer
function loadComponents(selector, filepath, cssPath = null, jsPath = null) {
    fetch(filepath).then(response => {
        if(!response.ok) {
            throw new Error(`Errore nel caricamento del ${filepath}`);
        }
        return response.text();
    }).then(content => {
        document.querySelector(selector).innerHTML = content;

        if(cssPath) {
            loadCss(cssPath);
        }

        if(jsPath) {
            loadJs(jsPath);
        }

    }).catch(error => console.error(error));
}

document.addEventListener('DOMContentLoaded', () => {
    loadComponents(
        "#header",
        "/BiteLine/Frontend/components/Header/header.html",
        "/BiteLine/Frontend/assets/css/styleComponent/headerStyle.css",
        "/BiteLine/Frontend/components/Header/headerScript.js"  // Nuovo parametro JS
    );

    loadComponents(
        "#footer",
        "/BiteLine/Frontend/components/Footer/footerComponent.html",
        "/BiteLine/Frontend/assets/css/styleComponent/footerStyle.css",
        "/BiteLine/Frontend/components/Footer/footer.js"  // Nuovo parametro JS
    );
});