<?php
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";
require __DIR__ . "/../../../../Backend/Controllers/Cookies/JWT_Verifier.php";

$jwtToken = getJwtToken();

if ($jwtToken) {
    // Verify JWT token and extract payload
    $payload = verifyJwtToken($jwtToken);

    if (!isset($payload['id'])) {
        die('User not authenticated');
    }

    if ($payload['role'] == 'owner'){
        header('Location: ../../errors/error-403.html');
    }

    $userId = $payload['id'];
} else {
    header('Location: ../../errors/error-403.html');
}

$resIdURL = $_GET['restaurant_id'];

$resDataRetiever = 'SELECT * FROM restaurants WHERE restaurant_id = ?';
$stmt = $mysqli->prepare($resDataRetiever);
$stmt->bind_param("i", $resIdURL);
$stmt->execute();
$result = $stmt->get_result();
$rowRes = $result->fetch_assoc();

$foodRetriever = 'SELECT * FROM products, menus 
         WHERE menu = menu_id 
           AND menus.restaurant_id = ?';

$stmt = $mysqli->prepare($foodRetriever);
$stmt->bind_param("i", $resIdURL);
$stmt->execute();
$result = $stmt->get_result();

$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | BiteLine</title>

    <link rel="stylesheet" href="../../../assets/css/OrderBoard.css">
</head>

<body>
<div class="overlay" id="overlay"></div>
<div class="mobile-cart-overlay" id="mobileCartOverlay">
    <div class="mobile-cart-panel">
        <div class="mobile-cart-header">
            <div class="mobile-cart-title">Il tuo ordine:</div>
            <div class="mobile-cart-close" id="mobileCartClose">×</div>
        </div>
        <div class="mobile-cart-items" id="mobileCartItems">
            <!-- Mobile cart items will be displayed here -->
        </div>
        <div class="mobile-cart-footer">
            <div class="cart-total">
                <div class="total-label">Totale:</div>
                <div class="total-amount" id="mobileCartTotal">€0.00</div>
            </div>
            <button class="checkout-btn" id="mobileCheckoutBtn" disabled>Checkout</button>
        </div>
    </div>
</div>

<div class="toast" id="toast">Aggiunto al carrello</div>

<div class="app-container">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">M</div>
                <div class="logo-text"><?php echo $rowRes["name"] ?></div>
            </div>
            <div class="close-sidebar" id="closeSidebar">✕</div>
        </div>
        <div class="menu-categories">
        <?php
            $categories = array_unique(array_column($menu_items, 'category'));
            foreach ($categories as $category) {
                echo "
                        <div class=\"category\">
                            <div class=\"category-icon\">◆</div>
                            <div class=\"category-name\">" . htmlspecialchars($category) ."</div>
                        </div>
                ";
            }

        ?>

            <div class="category">
                <div class="category-icon">◆</div>
                <div class="category-name"><?php echo "<a href=\"../managing/applyRestaurant.php?restaurant_id=" . $resIdURL . "\">Invia candidatura</a>" ?></div>
            </div>
        </div>


        <div class="cart-area">
            <div class="cart-header">
                <div class="cart-title">Il tuo ordine:</div>
                <div class="cart-count" id="cartCount">0</div>
            </div>
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be displayed here -->
                <div class="cart-empty" id="cartEmpty">Carrello vuoto</div>
            </div>
            <div class="cart-total">
                <div class="total-label">Totale:</div>
                <div class="total-amount" id="cartTotal">€0.00</div>
            </div>
            <button class="checkout-btn" id="checkoutBtn" disabled>Checkout</button>
        </div>
    </div>

    <div class="content">
        <div class="mobile-header">
            <div class="menu-toggle" id="menuToggle">☰</div>
            <div class="mobile-logo"><?php echo $rowRes["name"] ?></div>
            <div class="mobile-cart" id="mobileCartBtn">
                🛒
                <div class="mobile-cart-count" id="mobileCartCount">0</div>
            </div>
        </div>

        <div class="content-inner">
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-icon">🔍</div>
                    <input type="text" class="search-input" placeholder="Search menu...">
                </div>
            </div>

            <div class="category-header">
                <h2 class="category-title">Starters</h2>
                <p class="category-desc"></p>
            </div>

            <div class="menu-grid">
                <?php
                $foodRetriever = 'SELECT products.name AS product_name, products.description, products.price, 
                         products.image_path, products.category 
                  FROM products 
                  INNER JOIN menus ON products.menu = menus.menu_id 
                  WHERE menus.restaurant_id = ?';

                $stmt = $mysqli->prepare($foodRetriever);
                $stmt->bind_param("i", $resIdURL);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
    <div class=\"menu-card\">
        <img src=\"/BiteLine/Frontend/assets/media/images/users/menu/" . htmlspecialchars($row["image_path"]) . " \" 
             alt=\"" . htmlspecialchars($row["product_name"]) ."\" 
             class=\"food-img\">
        <div class=\"card-content\">
            <div class=\"food-tags\">
                <div class=\"food-tag\">" . htmlspecialchars($row["category"]) ."</div>
            </div>
            <h3 class=\"food-title\">" . htmlspecialchars($row["product_name"]) ."</h3>
            <p class=\"food-desc\">" . htmlspecialchars($row["description"]) ."</p>
            <div class=\"card-\">
                <div class=\"price\">€" . htmlspecialchars($row["price"]) ."</div>
                <div class=\"add-btn\">+</div>
            </div>
        </div>
    </div>
    ";
                    }
                }

?>



            </div>
        </div>
    </div>
</div>

<script src="../../../assets/script/orderScript.js"></script>
</body>

</html>