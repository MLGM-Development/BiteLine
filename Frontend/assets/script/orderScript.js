// DOM elements
const menuCards = document.querySelectorAll('.menu-card');
const menuToggle = document.getElementById('menuToggle');
const closeSidebar = document.getElementById('closeSidebar');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const mobileCartBtn = document.getElementById('mobileCartBtn');
const mobileCartOverlay = document.getElementById('mobileCartOverlay');
const mobileCartClose = document.getElementById('mobileCartClose');
const toast = document.getElementById('toast');
const categoryLinks = document.querySelectorAll('.category');
const filterChips = document.querySelectorAll('.filter-chip');

// Store the data for each category
const categoryData = {};

// Initialize category data from menu cards
function initializeCategoryData() {
    menuCards.forEach(card => {
        const category = card.querySelector('.food-tag').textContent;
        if (!categoryData[category]) {
            categoryData[category] = {
                description: generateDescription(category),
                items: []
            };
        }

        categoryData[category].items.push(card);
    });
}

// Generate descriptions based on category name
function generateDescription(category) {
    switch (category) {
        case "Antipasti":
            return "Inizia il tuo viaggio culinario con i nostri antipasti, preparati con ingredienti freschi e di stagione.";
        case "Primi piatti":
            return "Assapora la tradizione con i nostri primi piatti, un perfetto equilibrio tra ricette classiche e creatività moderna.";
        case "Secondi piatti":
            return "I nostri secondi piatti esaltano i migliori ingredienti con tecniche di cottura raffinate e sapori irresistibili.";
        case "Pizze":
            return "Gusta la vera pizza italiana, con impasto artigianale e condimenti di prima qualità, cotta alla perfezione.";
        case "Contorni":
            return "Aggiungi un tocco di gusto al tuo pasto con i nostri contorni, pensati per esaltare ogni portata.";
        case "Dolci":
            return "Concludi in dolcezza con i nostri dessert artigianali, un tripudio di sapori per coccolare il palato.";
        case "Bevande":
            return "Dissetati con la nostra selezione di bevande, perfette per accompagnare ogni momento del pasto.";
        default:
            return `Scopri la nostra selezione di ${category} preparati con ingredienti di alta qualità e tanta passione.`;
    }
}

// Filter menu items by category
function filterMenuByCategory(category) {
    menuCards.forEach(card => {
        const cardCategory = card.querySelector('.food-tag').textContent;
        if (category === 'All' || cardCategory === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Set active category
function setActiveCategory(categoryName) {
    // Update category title and description
    document.querySelector('.category-title').textContent = categoryName;

    // Get description from our data object or generate a new one
    const description = categoryData[categoryName] ?
        categoryData[categoryName].description :
        generateDescription(categoryName);

    document.querySelector('.category-desc').textContent = description;

    // Filter menu items
    filterMenuByCategory(categoryName);
}

// Initialize the cart system
const cart = {
    items: [],

    // Add item to cart
    addItem(id, name, price, img) {
        const existingItem = this.items.find(item => item.id === id);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.items.push({
                id,
                name,
                price,
                img,
                quantity: 1
            });
        }

        this.updateCart();
        return this.getTotalItems();
    },

    // Remove item from cart
    removeItem(id) {
        const index = this.items.findIndex(item => item.id === id);
        if (index !== -1) {
            this.items.splice(index, 1);
            this.updateCart();
        }
        return this.getTotalItems();
    },

    // Update item quantity
    updateQuantity(id, quantity) {
        const item = this.items.find(item => item.id === id);
        if (item) {
            if (quantity <= 0) {
                return this.removeItem(id);
            }
            item.quantity = quantity;
            this.updateCart();
        }
        return this.getTotalItems();
    },

    // Get total number of items
    getTotalItems() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    },

    // Get total price
    getTotalPrice() {
        return this.items.reduce((total, item) => {
            // More robust price parsing
            const priceText = item.price.trim();
            const priceNumber = priceText.replace(/[^0-9,\.]/g, '')  // Remove all non-numeric characters except comma and dot
                .replace(',', '.');  // Replace comma with dot for decimal
            return total + (parseFloat(priceNumber) * item.quantity);
        }, 0);
    },

    // Clear cart
    clearCart() {
        this.items = [];
        this.updateCart();
    },

    // Update cart display
    updateCart() {
        const cartItemsContainer = document.getElementById('cartItems');
        const mobileCartItemsContainer = document.getElementById('mobileCartItems');
        const cartCount = document.getElementById('cartCount');
        const mobileCartCount = document.getElementById('mobileCartCount');
        const cartTotal = document.getElementById('cartTotal');
        const mobileCartTotal = document.getElementById('mobileCartTotal');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const mobileCheckoutBtn = document.getElementById('mobileCheckoutBtn');

        applyStyles(cartItemsContainer, {
            'overflow-x': 'hidden',
            'width': '100%'
        });

        applyStyles(mobileCartItemsContainer, {
            'overflow-x': 'hidden',
            'width': '100%'
        });

        // Update cart count
        const totalItems = this.getTotalItems();
        cartCount.textContent = totalItems;
        mobileCartCount.textContent = totalItems;

        // Update cart items display
        if (totalItems === 0) {
            cartItemsContainer.innerHTML = '<div class="cart-empty" id="cartEmpty">Carrello vuoto</div>';
            mobileCartItemsContainer.innerHTML = '<div class="cart-empty">Carrello vuoto</div>';
            checkoutBtn.disabled = true;
            mobileCheckoutBtn.disabled = true;
        } else {
            checkoutBtn.disabled = false;
            mobileCheckoutBtn.disabled = false;

            // Render cart items
            cartItemsContainer.innerHTML = '';
            mobileCartItemsContainer.innerHTML = '';

            this.items.forEach(item => {
                const cartItemElement = document.createElement('div');
                cartItemElement.className = 'cart-item';

                applyStyles(cartItemElement, {
                    'box-sizing': 'border-box',
                    'width': '100%',
                    'max-width': '100%',
                    'overflow': 'hidden'
                });

                cartItemElement.innerHTML = `
                        <div class="cart-item-info">
                            <img src="${item.img}" alt="${item.name}" class="cart-item-img">
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">${item.price}</div>
                            </div>
                        </div>
                        <div class="cart-item-actions">
                            <div class="cart-item-quantity">
                                <div class="quantity-btn" data-action="decrease" data-id="${item.id}">-</div>
                                <div class="quantity-num">${item.quantity}</div>
                                <div class="quantity-btn" data-action="increase" data-id="${item.id}">+</div>
                            </div>
                            <div class="cart-item-remove" data-id="${item.id}">×</div>
                        </div>
                    `;

                // Add the item to desktop cart
                const desktopItem = cartItemElement.cloneNode(true);
                cartItemsContainer.appendChild(desktopItem);

                // Add the item to mobile cart
                mobileCartItemsContainer.appendChild(cartItemElement);
            });

            // Add event listeners to cart item buttons - needs to be done after adding items to DOM
            this.addCartItemEventListeners(cartItemsContainer);
            this.addCartItemEventListeners(mobileCartItemsContainer);
        }

        // Update total price
        const totalPrice = this.getTotalPrice();
        cartTotal.textContent = `€${totalPrice.toFixed(2)}`;
        mobileCartTotal.textContent = `€${totalPrice.toFixed(2)}`;
    },

    // Add event listeners to cart item buttons
    addCartItemEventListeners(container) {
        // Quantity decrease/increase buttons
        const quantityBtns = container.querySelectorAll('.quantity-btn');
        quantityBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const action = btn.getAttribute('data-action');
                const item = this.items.find(item => item.id === id);

                if (item) {
                    let newQuantity = item.quantity;
                    if (action === 'increase') {
                        newQuantity++;
                    } else if (action === 'decrease') {
                        newQuantity--;
                    }
                    this.updateQuantity(id, newQuantity);
                }
            });
        });

        // Remove item buttons
        const removeBtns = container.querySelectorAll('.cart-item-remove');
        removeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                this.removeItem(id);
            });
        });
    }
};

// Helper function to apply styles
function applyStyles(element, styles) {
    for (const property in styles) {
        element.style[property] = styles[property];
    }
}

// Add data-id attribute to menu cards if they don't have one
menuCards.forEach((card, index) => {
    if (!card.getAttribute('data-id')) {
        card.setAttribute('data-id', `item-${index}`);
    }
});

// Menu card click event
menuCards.forEach(card => {
    const addBtn = card.querySelector('.add-btn');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const id = card.getAttribute('data-id');
            const name = card.querySelector('.food-title').textContent;
            const price = card.querySelector('.price').textContent;
            const img = card.querySelector('.food-img').getAttribute('src');

            cart.addItem(id, name, price, img);
            showToast('Item added to cart');
        });
    }
});

// Mobile menu toggle
menuToggle.addEventListener('click', () => {
    sidebar.classList.add('active');
    overlay.classList.add('active');
});

// Close sidebar
closeSidebar.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

// Close sidebar when clicking overlay
overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

// Mobile cart button
mobileCartBtn.addEventListener('click', () => {
    mobileCartOverlay.classList.add('active');
});

// Close mobile cart
mobileCartClose.addEventListener('click', () => {
    mobileCartOverlay.classList.remove('active');
});

// Close mobile cart when clicking outside
mobileCartOverlay.addEventListener('click', (e) => {
    if (e.target === mobileCartOverlay) {
        mobileCartOverlay.classList.remove('active');
    }
});

// Category links
categoryLinks.forEach(link => {
    link.addEventListener('click', () => {
        // Remove active class from all links
        categoryLinks.forEach(l => l.classList.remove('active'));

        // Add active class to clicked link
        link.classList.add('active');

        // Get the category name
        const categoryName = link.querySelector('.category-name').textContent;

        // Update UI for selected category
        setActiveCategory(categoryName);

        // Close mobile sidebar
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
});

// Filter chips
if (filterChips && filterChips.length > 0) {
    filterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            // Remove active class from all chips
            filterChips.forEach(c => c.classList.remove('active'));

            // Add active class to clicked chip
            chip.classList.add('active');

            // Apply filter (in a real app, this would filter the menu items)
            const filter = chip.textContent;
            console.log(`Filter applied: ${filter}`);
        });
    });
}

// Toast notification function
function showToast(message) {
    toast.textContent = message;
    toast.classList.add('active');

    setTimeout(() => {
        toast.classList.remove('active');
    }, 3000);
}

// Checkout button
const checkoutBtn = document.getElementById('checkoutBtn');
const mobileCheckoutBtn = document.getElementById('mobileCheckoutBtn');

function getRestaurantIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('restaurant_id');
}

checkoutBtn.addEventListener('click', () => {
    // Store cart items in localStorage
    localStorage.setItem('bitelineCart', JSON.stringify({
        items: cart.items,
        totalPrice: cart.getTotalPrice(),
        restaurantId: getRestaurantIdFromUrl()
    }));

    // Redirect to checkout page
    window.location.href = 'checkout.html';
});

mobileCheckoutBtn.addEventListener('click', () => {
    // Store cart items in localStorage
    localStorage.setItem('bitelineCart', JSON.stringify({
        items: cart.items,
        totalPrice: cart.getTotalPrice(),
        restaurantId: getRestaurantIdFromUrl()
    }));

    // Redirect to checkout page
    window.location.href = 'checkout.html';
    mobileCartOverlay.classList.remove('active');
});

// Initialize the page
function initializePage() {
    // Set up category data
    initializeCategoryData();

    // Set the first category as active
    if (categoryLinks.length > 0) {
        const firstCategory = categoryLinks[0].querySelector('.category-name').textContent;
        categoryLinks[0].classList.add('active');
        setActiveCategory(firstCategory);
    }

    // Initialize cart
    cart.updateCart();
}

// Run initialization when page loads
document.addEventListener('DOMContentLoaded', initializePage);