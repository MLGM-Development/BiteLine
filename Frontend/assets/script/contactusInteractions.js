document.addEventListener('DOMContentLoaded', () => {
    const fadeElements = document.querySelectorAll('.fade-in-up');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(element => {
        observer.observe(element);
    });

    // Form submission
    const contactForm = document.getElementById('contactForm');
    const successMessage = document.getElementById('successMessage');

    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Simulate form submission
        setTimeout(() => {
            successMessage.classList.add('active');
            contactForm.reset();

            setTimeout(() => {
                successMessage.classList.remove('active');
            }, 5000);
        }, 1000);
    });


        // Hover effect for interactive elements

});