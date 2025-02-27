<?php
$mysqli = require __DIR__ . "/../../Backend/Database/connection.php";

$restaurantRetriever = "SELECT * FROM restaurants";
$restaurantRetrieverResult = $mysqli->query($restaurantRetriever);

$restaurantData = array();

while ($row = $restaurantRetrieverResult->fetch_assoc()) {
    $restaurantData[] = array(
        "name" => $row["name"],
        "image" => $row["image_path"],
        "description" => $row["description"],
        "rating" => $row["rating"],
        "cuisine" => $row["cuisine"],
        //"deliveryTime" => $row["delivery_time"]
    );
}

$mysqli->close();

?>


const featuredRestaurants = <?php echo json_encode($restaurantData) ?>

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
                            <span class="delivery-time">DA IMPOSTARE</span>
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