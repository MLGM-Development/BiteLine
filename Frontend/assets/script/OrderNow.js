// Sample restaurant data
const featuredRestaurants = [
    {
        name: "Trattoria Moderna",
        image: "/api/placeholder/800/500",
        description: "Autentica cucina italiana con un tocco contemporaneo. I nostri piatti sono preparati con ingredienti freschi e locali.",
        rating: 4.8,
        cuisine: "Italiana",
        deliveryTime: "25-35 min"
    },
    {
        name: "Sakura Sushi",
        image: "/api/placeholder/800/500",
        description: "Il miglior sushi in città con pesce fresco consegnato quotidianamente. Specialità di roll unici e sashimi di alta qualità.",
        rating: 4.7,
        cuisine: "Giapponese",
        deliveryTime: "30-40 min"
    },
    {
        name: "El Taqueria",
        image: "/api/placeholder/800/500",
        description: "Autentico street food messicano con ricette tradizionali. I nostri tacos e burritos sono leggendari.",
        rating: 4.6,
        cuisine: "Messicana",
        deliveryTime: "20-30 min"
    }
];

// Function to render restaurant cards
function renderRestaurants() {
    const container = document.getElementById('featured-restaurants');

    featuredRestaurants.forEach(restaurant => {
        const card = document.createElement('div');
        card.className = 'card';

        card.innerHTML = `
                    <div class="card-image">
                        <img src="${restaurant.image}" alt="${restaurant.name}">
                        <div class="rating">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            ${restaurant.rating}
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">${restaurant.name}</h3>
                        <p class="card-description">${restaurant.description}</p>
                        <div class="card-footer">
                            <span class="cuisine">${restaurant.cuisine}</span>
                            <span class="delivery-time">${restaurant.deliveryTime}</span>
                        </div>
                    </div>
                `;

        container.appendChild(card);
    });
}

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    renderRestaurants();

    // Add event listener for search input
    const searchInput = document.querySelector('.search-box input');
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            // In a real app, this would trigger a search
            alert(`Ricerca per: ${searchInput.value}`);
            searchInput.value = '';
        }
    });

    // Add event listener for search icon
    const searchIcon = document.querySelector('.search-icon');
    searchIcon.addEventListener('click', () => {
        const searchValue = document.querySelector('.search-box input').value;
        if (searchValue) {
            // In a real app, this would trigger a search
            alert(`Ricerca per: ${searchValue}`);
            document.querySelector('.search-box input').value = '';
        }
    });
});