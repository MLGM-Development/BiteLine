class bitelineHeader extends HTMLElement{
    connectedCallback(){
        this.innerHTML = `
        <header>
            <a href="../pages/admins/logAdmin.html">Link</a>
        </header>
        `
    }
}

customElements.define('biteline-header', bitelineHeader)