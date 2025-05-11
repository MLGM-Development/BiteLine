// Gallery functionality
const slides = document.querySelectorAll('.gallery-slide');
const thumbnails = document.querySelectorAll('.thumbnail');
const progressBar = document.querySelector('.progress-bar');
let currentSlide = 0;
let slideInterval;
const slideTime = 5000; // 5 seconds per slide

// Initialize the slider
function startSlider() {
    resetProgressBar();
    slideInterval = setInterval(nextSlide, slideTime);
}

function resetProgressBar() {
    progressBar.style.width = '0%';
    progressBar.style.transition = 'none';
    setTimeout(() => {
        progressBar.style.transition = `width ${slideTime}ms linear`;
        progressBar.style.width = '100%';
    }, 10);
}

function goToSlide(index) {
    // Clear current slide
    slides.forEach(slide => slide.classList.remove('active'));
    thumbnails.forEach(thumb => thumb.classList.remove('active'));

    // Set new slide
    slides[index].classList.add('active');
    thumbnails[index].classList.add('active');
    currentSlide = index;

    // Reset interval and progress bar
    clearInterval(slideInterval);
    startSlider();
}

function nextSlide() {
    const next = (currentSlide + 1) % slides.length;
    goToSlide(next);
}

// Add click events to thumbnails
thumbnails.forEach((thumb, index) => {
    thumb.addEventListener('click', () => goToSlide(index));
});

// Start the slider
startSlider();

// Scroll animations
const fadeElements = document.querySelectorAll('.fade-in');
const cards = document.querySelectorAll('.step-card');
const userCards = document.querySelectorAll('.user-card');
const finalElements = document.querySelectorAll('.final-title, .final-subtitle, .final-buttons');

function checkScroll() {
    const triggerBottom = window.innerHeight * 0.8;

    fadeElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        if (elementTop < triggerBottom) {
            element.classList.add('revealed');
        }
    });

    // Check if timeline is in view
    const timeline = document.querySelector('.timeline-progress');
    if (timeline) {
        const timelineTop = timeline.getBoundingClientRect().top;
        const timelineBottom = timeline.getBoundingClientRect().bottom;

        if (timelineTop < triggerBottom && timelineBottom > 0) {
            timeline.classList.add('revealed');
        }
    }

    cards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        if (cardTop < triggerBottom) {
            setTimeout(() => {
                card.classList.add('revealed');
            }, index * 300); // Staggered animation
        }
    });

    userCards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        if (cardTop < triggerBottom) {
            setTimeout(() => {
                card.classList.add('revealed');
            }, index * 300); // Staggered animation
        }
    });

    finalElements.forEach((element, index) => {
        const elementTop = element.getBoundingClientRect().top;
        if (elementTop < triggerBottom) {
            element.classList.add('revealed');
        }
    });
}

// Initial check
checkScroll();

// Check on scroll
window.addEventListener('scroll', checkScroll);

// Smooth scroll for navigation
document.querySelector('.cta-button').addEventListener('click', () => {
    document.getElementById('how-it-works').scrollIntoView({ behavior: 'smooth' });
});

document.querySelector('.scroll-indicator').addEventListener('click', () => {
    document.getElementById('how-it-works').scrollIntoView({ behavior: 'smooth' });
});