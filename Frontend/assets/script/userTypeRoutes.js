document.addEventListener('DOMContentLoaded', function() {
    // Get card elements
    const personalCard = document.getElementById('personal-card');
    const businessCard = document.getElementById('business-card');
    const developerCard = document.getElementById('developer-card');

    // Add click event listeners to redirect to respective registration pages
    personalCard.addEventListener('click', function() {
        window.location.href = '/register/personal';
    });

    businessCard.addEventListener('click', function() {
        window.location.href = '/register/business';
    });

    developerCard.addEventListener('click', function() {
        window.location.href = '/register/developer';
    });

    // Add ripple effect on button click
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent card click event

            const card = this.closest('.card');
            if (card.id === 'personal-card') {
                window.location.href = '/BiteLine/Frontend/pages/users/Customers/RegCustomer.html';
            } else if (card.id === 'business-card') {
                window.location.href = '/BiteLine/Frontend/pages/users/Owners/RegOwner.html';
            } else if (card.id === 'developer-card') {
                window.location.href = '/BiteLine/Frontend/pages/admins/registerAdmin.html';
            }
        });
    });
});