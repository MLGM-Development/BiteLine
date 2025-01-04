class bitelineFooter extends HTMLElement{
    connectedCallback(){
        this.innerHTML = `
        <footer>
            <h1>CiaoFooter</h1>
        </footer>
        `
    }
}

customElements.define('biteline-footer', bitelineFooter)