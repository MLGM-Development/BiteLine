<?php
// Insert this at the top of your confirmation.php file
$orderId = $_GET['order_id'] ?? 'Unknown';
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";
require __DIR__ . "/../../../../Backend/Controllers/Cookies/JWT_Verifier.php";
$mail = require __DIR__ . "/../../../../Backend/sender/mailer.php";

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

$sqlUserRetrieval = "SELECT email FROM users WHERE user_id = ?";
$stmt = $mysqli->prepare($sqlUserRetrieval);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
    $mail->setFrom('bitelineservices@gmail.com');
    $mail->addAddress($email);
    $mail->Subject = 'Conferma Ordine';
    $mail->Body = <<<END
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Ordine | BiteLine</title>
    <link rel="shortcut icon" href="../../../assets/media/images/favicon/favicon.png" type="image/x-icon">
    <style>
    :root {
    --primary: #8A2BE2;
    --success: #00E676;
    --background: #121212;
    --surface: #1E1E1E;
    --text-primary: #FFFFFF;
    --text-secondary: #B0B0B0;
    --accent: #BB86FC;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background-color: var(--background);
    color: var(--text-primary);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.confirmation-container {
    max-width: 600px;
    width: 90%;
    margin: 40px auto;
    padding: 40px;
    background-color: var(--surface);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.confirmation-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
}

.success-icon {
    width: 110px;
    height: 110px;
    margin: 20px auto 30px;
    background: radial-gradient(circle, rgba(0,230,118,0.2) 0%, rgba(0,230,118,0.1) 70%, transparent 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.success-icon::after {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    border: 2px solid var(--success);
    border-radius: 50%;
}

.success-icon svg {
    width: 50px;
    height: 50px;
    fill: var(--success);
    z-index: 1;
}

h1 {
    color: var(--success);
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 28px;
}

p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 15px;
}

.order-number {
    font-size: 24px;
    font-weight: 500;
    margin: 25px 0;
    padding: 12px;
    background-color: rgba(138, 43, 226, 0.1);
    border-radius: 8px;
    display: inline-block;
    color: var(--accent);
}

.button {
    display: inline-block;
    padding: 14px 32px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    margin-top: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(138, 43, 226, 0.3);
}

.button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(138, 43, 226, 0.4);
}

.company-logo {
    margin-top: 40px;
    opacity: 0.7;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 1px;
}
</style>
</head>
<body>
<div class="confirmation-container">
    <h1>Ordine Effettuato con Successo!</h1>
    <p>Grazie per il tuo ordine. Abbiamo ricevuto il tuo pagamento e stiamo preparando il tuo cibo.</p>
    <div class="order-number">Ordine #$orderId</div>
    <div class="company-logo">BiteLine</div>
</div>
</body>
</html>
END;

    $mail->isHTML(true);
    $mail->send();
} else {
    echo "Nessun utente trovato con questo ID.";
}




?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Ordine | BiteLine</title>
    <link rel="shortcut icon" href="../../../assets/media/images/favicon/favicon.png" type="image/x-icon">
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