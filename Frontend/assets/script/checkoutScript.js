document.addEventListener('DOMContentLoaded', function() {
    // Card interactive elements
    const creditCard = document.getElementById('credit-card');
    const cardNumberInput = document.getElementById('card-number');
    const cardNumberDisplay = document.getElementById('card-number-display');
    const cardHolderInput = document.getElementById('card-holder');
    const cardHolderDisplay = document.getElementById('card-holder-display');
    const expiryDateInput = document.getElementById('expiry-date');
    const expiryDateDisplay = document.getElementById('card-expiry-display');
    const cvcInput = document.getElementById('cvc');
    const cvcDisplay = document.getElementById('card-cvc-display');
    const cvcError = document.getElementById('cvc-error');
    const submitButton = document.getElementById('submit-button');
    const paymentMethods = document.querySelectorAll('.payment-method');
    const cardPaymentSection = document.getElementById('card-payment-section');
    const cartData = JSON.parse(localStorage.getItem('bitelineCart')) || { items: [], totalPrice: 0 };

    const orderItemsContainer = document.querySelector('.order-items');
    orderItemsContainer.innerHTML = ''; // Clear sample items

    if (cartData.items.length === 0) {
        // If cart is empty, redirect back to menu
        alert('Your cart is empty. Please add items to your cart before checkout.');
        window.location.href = '../../restaurants/menu.php?restaurant_id=' + cartData.restaurantId;
        return;
    }

    // Add each item to the order summary
    let subtotal = 0;
    cartData.items.forEach(item => {
        // Clean price string and convert to number
        const priceText = item.price.trim();
        const priceNumber = parseFloat(priceText.replace(/[^0-9,\.]/g, '').replace(',', '.'));
        const itemTotal = priceNumber * item.quantity;
        subtotal += itemTotal;

        // Create and append item element
        const itemElement = document.createElement('div');
        itemElement.className = 'order-item';
        itemElement.innerHTML = `
            <div class="order-item-image">
                <img src="${item.img}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover;">
            </div>
            <div class="order-item-details">
                <div class="order-item-name">${item.name} x ${item.quantity}</div>
                <div class="order-item-price">€${itemTotal.toFixed(2)}</div>
            </div>
        `;
        orderItemsContainer.appendChild(itemElement);
    });

    // Calculate and display order totals
    const shipping = 2.99;
    const tax = subtotal * 0.10; // 10% tax
    const total = subtotal + shipping + tax;

    // Update the totals in the UI
    const totalsContainer = document.querySelector('.order-totals');
    totalsContainer.innerHTML = `
        <div class="order-total-row">
            <div>Subtotale</div>
            <div>€${subtotal.toFixed(2)}</div>
        </div>
        <div class="order-total-row">
            <div>Spedizione</div>
            <div>€${shipping.toFixed(2)}</div>
        </div>
        <div class="order-total-row">
            <div>Tasse (10%)</div>
            <div>€${tax.toFixed(2)}</div>
        </div>
        <div class="order-total-row final">
            <div>Totale</div>
            <div>€${total.toFixed(2)}</div>
        </div>
    `;

    // Store the final order details for submission
    localStorage.setItem('bitelineFinalOrder', JSON.stringify({
        items: cartData.items,
        subtotal: subtotal,
        shipping: shipping,
        tax: tax,
        total: total,
        restaurantId: cartData.restaurantId
    }));

    // Format card number with spaces
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';

        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }

        e.target.value = formattedValue;

        // Update card display
        cardNumberDisplay.textContent = formattedValue || '•••• •••• •••• ••••';

        // Detect card type and update visuals
        if (value.startsWith('4')) {
            // Visa
            creditCard.querySelector('.credit-card-front').style.background = 'linear-gradient(135deg, #1a237e, #0d47a1)';
        } else if (value.startsWith('5')) {
            // Mastercard
            creditCard.querySelector('.credit-card-front').style.background = 'linear-gradient(135deg, #f44336, #d32f2f)';
        } else if (value.startsWith('3')) {
            // Amex
            creditCard.querySelector('.credit-card-front').style.background = 'linear-gradient(135deg, #2196f3, #1976d2)';
        } else if (value.startsWith('6')) {
            // Discover
            creditCard.querySelector('.credit-card-front').style.background = 'linear-gradient(135deg, #ff9800, #f57c00)';
        } else {
            // Default
            creditCard.querySelector('.credit-card-front').style.background = 'linear-gradient(135deg, #303f9f, #1a237e)';
        }
    });

    // Update card holder name
    cardHolderInput.addEventListener('input', function(e) {
        cardHolderDisplay.textContent = e.target.value.toUpperCase() || 'YOUR NAME';
    });

    // Format expiry date
    expiryDateInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/gi, '');

        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2);
        }

        e.target.value = value;
        expiryDateDisplay.textContent = value || 'MM/YY';
    });

    // Handle CVC input and card flip
    cvcInput.addEventListener('focus', function() {
        creditCard.classList.add('flipped');
    });

    cvcInput.addEventListener('blur', function() {
        creditCard.classList.remove('flipped');
    });

    cvcInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/gi, '');
        e.target.value = value;
        cvcDisplay.textContent = value || '•••';

        if (value.length < 3) {
            cvcError.textContent = 'CVC must be 3 digits';
            cvcInput.classList.add('error');
        } else {
            cvcError.textContent = '';
            cvcInput.classList.remove('error');
        }
    });

    // Payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));

            // Add active class to clicked method
            this.classList.add('active');

            // Show/hide card section based on selection
            if (this.dataset.method === 'card') {
                cardPaymentSection.style.display = 'block';
            } else {
                cardPaymentSection.style.display = 'none';
            }
        });
    });

    // Form submission
    submitButton.addEventListener('click', function() {
        // Simple validation
        let isValid = true;

        if (cardNumberInput.value.length < 19) {
            isValid = false;
            cardNumberInput.classList.add('error');
        } else {
            cardNumberInput.classList.remove('error');
        }

        if (cardHolderInput.value.trim() === '') {
            isValid = false;
            cardHolderInput.classList.add('error');
        } else {
            cardHolderInput.classList.remove('error');
        }

        if (expiryDateInput.value.length < 5) {
            isValid = false;
            expiryDateInput.classList.add('error');
        } else {
            expiryDateInput.classList.remove('error');
        }

        if (cvcInput.value.length < 3) {
            isValid = false;
            cvcInput.classList.add('error');
            cvcError.textContent = 'CVC must be 3 digits';
        } else {
            cvcInput.classList.remove('error');
            cvcError.textContent = '';
        }

        if (isValid) {
            // Simulate successful payment
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;

            setTimeout(() => {
                submitButton.textContent = 'Payment Successful ✓';
                submitButton.style.backgroundColor = 'var(--success)';

                // Redirect after payment
                setTimeout(() => {
                    alert('Payment processed successfully! Thank you for your order.');
                }, 1500);
            }, 2000);
        }
    });

    submitButton.addEventListener('click', function() {
        // Simple validation
        let isValid = true;
        const activePaymentMethod = document.querySelector('.payment-method.active').dataset.method;

        if (activePaymentMethod === 'card') {
            // Validate card details
            if (cardNumberInput.value.length < 19) {
                isValid = false;
                cardNumberInput.classList.add('error');
            } else {
                cardNumberInput.classList.remove('error');
            }

            if (cardHolderInput.value.trim() === '') {
                isValid = false;
                cardHolderInput.classList.add('error');
            } else {
                cardHolderInput.classList.remove('error');
            }

            if (expiryDateInput.value.length < 5) {
                isValid = false;
                expiryDateInput.classList.add('error');
            } else {
                expiryDateInput.classList.remove('error');
            }

            if (cvcInput.value.length < 3) {
                isValid = false;
                cvcInput.classList.add('error');
                cvcError.textContent = 'CVC must be 3 digits';
            } else {
                cvcInput.classList.remove('error');
                cvcError.textContent = '';
            }
        }

        // Check address fields
        const billingAddress = document.getElementById('billing-address');
        const city = document.getElementById('city');
        const postalCode = document.getElementById('postal-code');

        if (billingAddress.value.trim() === '') {
            isValid = false;
            billingAddress.classList.add('error');
        } else {
            billingAddress.classList.remove('error');
        }

        if (city.value.trim() === '') {
            isValid = false;
            city.classList.add('error');
        } else {
            city.classList.remove('error');
        }

        if (postalCode.value.trim() === '') {
            isValid = false;
            postalCode.classList.add('error');
        } else {
            postalCode.classList.remove('error');
        }

        if (isValid) {
            // Get the final order details
            const orderDetails = JSON.parse(localStorage.getItem('bitelineFinalOrder'));

            // Here you would typically send this data to your server
            // For now, we'll simulate the process

            // Simulate successful payment
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;

            setTimeout(() => {
                submitButton.textContent = 'Payment Successful ✓';
                submitButton.style.backgroundColor = 'var(--success)';

                // Clear the cart after successful payment
                localStorage.removeItem('bitelineCart');

                // Redirect after payment
                setTimeout(() => {
                    alert('Payment processed successfully! Thank you for your order.');
                    // Redirect to a confirmation page or back to the restaurant
                    window.location.href = '/BiteLine/Frontend/pages/location/OrderNow/confirmation.php?order_id=' + generateOrderId();
                }, 1500);
            }, 2000);
        }
    });

// Helper function to generate a random order ID
    function generateOrderId() {
        return Math.floor(100000 + Math.random() * 900000); // 6 digit order number
    }

});