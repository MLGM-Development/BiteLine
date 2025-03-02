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
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Ordine | BiteLine</title>
    <link rel="stylesheet" href="../../../assets/css/confirmOrderStyle.css">
</head>
<body>
<div class="confirmation-container">
    <div class="success-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
        </svg>
    </div>
    <h1>Ordine Effettuato con Successo!</h1>
    <p>Grazie per il tuo ordine. Abbiamo ricevuto il tuo pagamento e stiamo preparando il tuo cibo.</p>
    <div class="order-number">Ordine #<?php echo htmlspecialchars($orderId); ?></div>
    <p>Riceverai a breve un'email di conferma.</p>
    <a href="OrderPage.html" class="button">Torna alla pagina di ordinazione</a>
    <div class="company-logo">BiteLine</div>
</div>
</body>
</html>