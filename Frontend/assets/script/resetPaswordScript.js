if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
    document.body.style.setProperty('--bg-primary', '#f5f5f7');
    document.body.style.setProperty('--bg-secondary', '#ffffff');
    document.body.style.setProperty('--text-primary', '#1d1d1f');
    document.body.style.setProperty('--text-secondary', '#4c4c4c');
    // L'accento rimane lo stesso
}