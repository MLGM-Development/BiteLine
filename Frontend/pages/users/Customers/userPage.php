<?php
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";
require __DIR__ . '/../../../../Backend/Controllers/Cookies/JWT_Verifier.php';

$jwtToken = getJwtToken();

if ($jwtToken) {
    // Verify JWT token and extract payload
    $payload = verifyJwtToken($jwtToken);

    if (!isset($payload['id'])) {
        die('User not authenticated');
    }

    $userId = $payload['id'];
} else {
    header('Location: ../session/auth-login.html');
}

$userId = $payload['id'];

$isOwner = false;
$userDataRetriever = 'SELECT * FROM users WHERE user_id = ?';
$statement = $mysqli->prepare($userDataRetriever);
$statement->bind_param('i', $userId);
$statement->execute();
$result = $statement->get_result();

if ($result->num_rows === 0) {
    $isOwner = true;
    $userDataRetriever = 'SELECT * FROM owners WHERE owner_id = ?';
    $statement = $mysqli->prepare($userDataRetriever);
    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
}

$userData = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../assets/css/universal/reset.css">
    <link rel="stylesheet" href="../../../assets/css/UserPageStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <title>Profilo Personale | BiteLine</title>
</head>
<body>

<header id="header"></header>

<div class="container">
    <div class="profile-header">
        <h1 class="profile-title">Profilo Utente</h1>
        <p class="profile-subtitle">Visualizza le tue informazioni</p>
    </div>

    <div class="logout-wrapper">
        <button id="lobtn">Logout</button>
    </div>

    <div class="profile-grid">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informazioni personali</h2>
            </div>
            <div class="personal-info">
                <div class="info-group">
                    <div class="info-label">Nome e cognome</div>
                    <div class="info-value"><?php echo $userData["name"] . " " . $userData["l_name"] ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo $userData["email"]?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Membro dal</div>
                    <div class="info-value"><?php echo date("d-m-Y", strtotime($userData["created_at"])) ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Tipo di utente:</div>
                    <div class="info-value"><?php
                            if($isOwner)    {
                                echo "Proprietario";
                            } else {
                                echo "Cliente";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!$isOwner): ?>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ordini recenti</h2>
            </div>

            <?php
            // Query per recuperare gli ordini e i prodotti associati
            $recentOrderretriever = 'SELECT orders.order_id, orders.restaurant_id, orders.order_date, orders.order_time, orders.status, 
                                    order_items.quantity, products.name AS product_name
                             FROM orders, order_items, products
                             WHERE orders.user_id = ? 
                             AND orders.order_id = order_items.order_id 
                             AND order_items.product_id = products.product_id
                             ORDER BY orders.order_date DESC, orders.order_time DESC 
                             LIMIT 3';

            $statement = $mysqli->prepare($recentOrderretriever);
            $statement->bind_param('i', $userId);
            $statement->execute();
            $resultOrder = $statement->get_result();

            $orders = [];

            while ($row = $resultOrder->fetch_assoc()) {
                $orderId = $row['order_id'];
                $restaurantId = $row['restaurant_id'];

                // Se l'ordine non è ancora stato aggiunto all'array, lo inizializziamo
                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'restaurant_id' => $restaurantId,
                        'date' => $row['order_date'],
                        'time' => $row['order_time'],
                        'status' => $row['status'],
                        'items' => [],
                        'restaurant_name' => null // Inizialmente vuoto, lo riempiamo con la seconda query
                    ];
                }

                // Aggiungiamo i prodotti all'ordine
                $orders[$orderId]['items'][] = $row['quantity'] . "× " . $row['product_name'];
            }

            // Ora recuperiamo i nomi dei ristoranti con un'altra query
            foreach ($orders as $orderId => &$order) {
                $restaurantQuery = 'SELECT name AS restaurant_name FROM restaurants WHERE restaurant_id = ?';
                $stmt = $mysqli->prepare($restaurantQuery);
                $stmt->bind_param('i', $order['restaurant_id']);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $order['restaurant_name'] = $row['restaurant_name'];
                }
            }
            ?>

            <div class="order-list">
                <?php if (empty($orders)): ?>
                    <p>Nessun ordine recente.</p>
                <?php else: ?>
                    <?php foreach ($orders as $orderId => $order): ?>
                        <div class="order-item">
                            <div class="order-restaurant"><?= htmlspecialchars($order['restaurant_name']) ?></div>
                            <div class="order-details">
                                <div class="order-info order-date"><?= date('d-m-Y H:i', strtotime($order['date'] . ' ' . $order['time'])) ?></div>
                            </div>
                            <div class="order-items"><?= implode(", ", $order['items']) ?></div>
                            <span class="order-status"><?= ucfirst($order['status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>


            <div class="card">
            <div class="card-header">
                <h2 class="card-title">Statistiche ordini</h2>
            </div>
            <div class="stats-container">
                <?php
                    $totalOrdersQuery = 'SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?';
                    $stmt = $mysqli->prepare($totalOrdersQuery);
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $order = $stmt->get_result();
                    if ($row = $order->fetch_assoc()):
                ?>
                <div class="stat-card">
                    <div class="stat-value"> <?php echo $row["total_orders"]?> </div>
                    <div class="stat-label">Ordini Totali</div>
                </div>
                <?php endif;
                    $totalSpentQuery = 'SELECT SUM(total_amount) AS total_spent FROM orders WHERE user_id = ?';
                    $stmt = $mysqli->prepare($totalSpentQuery);
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $spent = $stmt->get_result();
                    if($row = $spent->fetch_assoc()):
                ?>
                <div class="stat-card">
                    <div class="stat-value"><?php echo "€" . $row["total_spent"]?></div>
                    <div class="stat-label">Spesa totale</div>
                </div>
                <?php
                    endif;
                    $totalTriedRestaurant = "SELECT COUNT(DISTINCT orders.restaurant_id) AS total_restaurants
                                                    FROM orders
                                                    WHERE orders.user_id = ?
                    ";
                    $stmt = $mysqli->prepare($totalTriedRestaurant);
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $restaurant = $stmt->get_result();
                    if($row = $restaurant->fetch_assoc()):
                ?>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $row["total_restaurants"]?></div>
                    <div class="stat-label">Ristoranti provati</div>
                </div>
                <?php endif; ?>
                <div class="stat-card">
                    <div class="stat-value">4.8</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>

                <div class="favorite-restaurant">
                    <div class="badge">Più frequente</div>

                    <?php
                    // Query per ottenere il ristorante più ordinato
                    $mostOrdered = 'SELECT restaurants.restaurant_id, name AS restaurant_name, total_orders, cuisine
                        FROM restaurants, (
                            SELECT restaurant_id, COUNT(order_id) AS total_orders
                            FROM orders
                            WHERE user_id = ?
                            GROUP BY restaurant_id
                            ORDER BY total_orders DESC
                            LIMIT 1
                        ) AS subquery
                        WHERE restaurants.restaurant_id = subquery.restaurant_id;';

                    $stmt = $mysqli->prepare($mostOrdered);
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $favouriteRest = $stmt->get_result();

                    if ($row = $favouriteRest->fetch_assoc()):
                        $mostOrderedRestaurantId = $row["restaurant_id"]; // Salviamo l'ID corretto
                        ?>
                        <div class="restaurant-header">
                            <div class="restaurant-logo">
                                <?php echo substr($row["restaurant_name"], 0, 2); ?>
                            </div>
                            <div class="restaurant-info">
                                <div class="restaurant-name"><?php echo htmlspecialchars($row["restaurant_name"]); ?></div>
                                <div class="restaurant-category"><?php echo htmlspecialchars($row["cuisine"]); ?></div>
                            </div>
                        </div>
                    <?php
                    endif;

                    // Query per ottenere statistiche sul ristorante più ordinato
                    $addedData = 'SELECT COUNT(*) AS total_orders, SUM(total_amount) AS total_spent, MAX(order_date) AS last_order
                      FROM orders 
                      WHERE restaurant_id = ? AND user_id = ?';

                    $stmt = $mysqli->prepare($addedData);
                    $stmt->bind_param('ii', $mostOrderedRestaurantId, $userId);
                    $stmt->execute();
                    $results = $stmt->get_result();

                    if ($row = $results->fetch_assoc()):
                        ?>
                        <div class="restaurant-stats">
                            <div class="restaurant-stat">
                                <div class="restaurant-stat-label">Ordini</div>
                                <div class="restaurant-stat-value"><?php echo $row["total_orders"]; ?></div>
                            </div>
                            <div class="restaurant-stat">
                                <div class="restaurant-stat-label">Spesa</div>
                                <div class="restaurant-stat-value">€ <?php echo number_format($row["total_spent"], 2, ',', '.'); ?></div>
                            </div>
                            <div class="restaurant-stat">
                                <div class="restaurant-stat-label">Ultimo ordine</div>
                                <div class="restaurant-stat-value">
                                    <?php echo date('d-m-Y', strtotime($row["last_order"])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
                <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ordinati più frequentemente</h2>
            </div>
            <div class="order-list">
                <?php
                    $mostFrequentlyOrdered = 'SELECT products.name AS product_name, restaurants.name AS restaurant_name, products.price, COUNT(order_items.product_id) AS total_orders FROM orders
                                                JOIN order_items ON orders.order_id = order_items.order_id
                                                JOIN products ON order_items.product_id = products.product_id
                                                JOIN restaurants ON orders.restaurant_id = restaurants.restaurant_id
                                                WHERE orders.user_id = ?
                                                GROUP BY order_items.product_id, restaurants.restaurant_id
                                                ORDER BY total_orders DESC
                                                LIMIT 5;
                                                ';
                    $stmt = $mysqli->prepare($mostFrequentlyOrdered);
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    if($results->num_rows>0)
                        while ($row = $results->fetch_assoc()):
                ?>
                <div class="order-item">
                    <div class="order-restaurant"><?php echo $row["restaurant_name"]?></div>
                    <div class="order-details">
                        <div class="order-info"><?php echo $row["product_name"]?></div>
                        <div class="order-info order-amount"><?php echo $row["price"]?></div>
                    </div>
                    <div class="order-items"><?php echo $row["total_orders"] ?></div>
                </div>
                <?php
                        endwhile;
                    else
                        echo "<p>Nessun prodotto trovato</p>";
                    endif;
                ?>
            </div>
        </div>
    </div>

</div>

<footer id="footer"></footer>

<script src="../../../assets/script/logout.js"></script>
<script src="../../../routes/componentLoader.js"></script>

</body>
</html>