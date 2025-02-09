let headerDD = false;
let respheaderDD = false;
const header = document.querySelector("header");
const respButton = document.getElementById("respMenuButton");
const respBlock = document.getElementById("respBlock1");
const respbuttonText = document.getElementById("respButtonText");
const dotH = document.getElementById('dotH');

function handleDesktopBehavior() {
    header.addEventListener('click', toggleHeader);
    respButton.removeEventListener('click', toggleMobileMenu);
}

function handleMobileBehavior() {
    header.removeEventListener('click', toggleHeader);
    respButton.addEventListener('click', toggleMobileMenu);
    respBlock.style.transform = 'translateY(10%)';
    respBlock.style.opacity = '0';
    respheaderDD = false;
    respbuttonText.textContent = 'MENU';
}

function toggleHeader() {
    headerDD = !headerDD;
    header.style.transform = headerDD ?  'translateY(0)' : 'translateY(-100px)';
}

function toggleMobileMenu() {
    respheaderDD = !respheaderDD;
    respBlock.style.transform = respheaderDD ? 'translateY(0)' : 'translateY(10%)';
    respBlock.style.opacity = respheaderDD ? '1' : '0';
    respbuttonText.textContent = respheaderDD ? 'CHIUDI' : 'MENU';
}

function handleResponsive() {
    if (window.innerWidth > 999) {
        handleDesktopBehavior();
    } else {
        headerDD = true;
        header.style.transform = 'translateY(0)';
        handleMobileBehavior();
    }
}

handleResponsive();
function debounce(func, timeout = 100) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), timeout);
    };
}

window.addEventListener('resize', debounce(handleResponsive));

const fileName = window.location.pathname.split('/').pop();
dotH.style.display = fileName === 'ownIndexDash.php' ? 'block' : 'none';