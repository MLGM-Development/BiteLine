<?php
// Insert this at the top of your confirmation.php file
$orderId = $_GET['order_id'] ?? 'Unknown';
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";
require __DIR__ . "/../../../../Backend/Controllers/Cookies/JWT_Verifier.php";

// Get user ID from JWT token
$jwtToken = getJwtToken();
$userId = null;

if ($jwtToken) {
    // Verify JWT token and extract payload
    $payload = verifyJwtToken($jwtToken);

    if (isset($payload['id'])) {
        $userId = $payload['id'];
    } else {
        die('User not authenticated');
    }
} else {
    header('Location: ../../errors/error-403.html');
    exit;
}

// Get order details from localStorage via JavaScript
echo "
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Retrieve the order details from localStorage
    const orderDetails = JSON.parse(localStorage.getItem('bitelineFinalOrder'));
    
    if (orderDetails) {
        // Send the order details to the server via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_order.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (this.status === 200) {
                console.log('Order saved successfully');
                // Clear the localStorage after successful order insertion
                localStorage.removeItem('bitelineFinalOrder');
            } else {
                console.error('Error saving order');
            }
        };
        xhr.send(JSON.stringify({
            orderId: '$orderId',
            userId: $userId,
            restaurantId: orderDetails.restaurantId,
            items: orderDetails.items,
            subtotal: orderDetails.subtotal,
            shipping: orderDetails.shipping,
            tax: orderDetails.tax,
            total: orderDetails.total
        }));
    }
});
</script>
";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | BiteLine</title>
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 20px auto;
            background-color: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon svg {
            width: 60px;
            height: 60px;
            fill: white;
        }

        h1 {
            color: #4caf50;
            margin-bottom: 20px;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ff5722;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="confirmation-container">
    <div class="success-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
        </svg>
    </div>
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your order. We've received your payment and are preparing your food.</p>
    <div class="order-number">Order #<?php echo htmlspecialchars($orderId); ?></div>
    <p>You will receive an email confirmation shortly.</p>
    <a href="OrderPage.html" class="button">Return to Dashboard</a>
</div>
</body>
</html>