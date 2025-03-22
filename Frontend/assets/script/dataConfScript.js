// Set current date
document.getElementById('current-date').innerText = new Date().toLocaleDateString();

// Create confetti effect
function createConfetti() {
    const colors = ['#6366f1', '#10b981', '#f59e0b', '#ec4899'];
    const header = document.querySelector('.header');

    for (let i = 0; i < 30; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.animationDuration = (Math.random() * 2 + 1) + 's';
        confetti.style.animationDelay = (Math.random() * 0.5) + 's';
        confetti.style.animationName = 'confetti-fall';
        confetti.style.animationFillMode = 'forwards';
        confetti.style.animationTimingFunction = 'ease-out';
        confetti.style.animationIterationCount = '1';

        header.appendChild(confetti);
    }
}

// Run animation when page loads
window.addEventListener('load', function() {
    createConfetti();
});