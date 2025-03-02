<?php
// save_order.php - Handles the AJAX request to save the order

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to database
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";

// Get raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input and log data for debugging
if (!$data) {
    error_log("Invalid JSON data received: " . $json);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

if (!isset($data['orderId']) || !isset($data['userId']) || !isset($data['restaurantId']) || !isset($data['items'])) {
    error_log("Missing required fields in data: " . json_encode($data));
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Start a transaction
$mysqli->begin_transaction();

try {
    // 1. Insert into orders table
    $orderQuery = "INSERT INTO orders (order_id, user_id, restaurant_id, subtotal, shipping_fee, tax, total_amount, status, order_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'In attesa', NOW())";

    $stmt = $mysqli->prepare($orderQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("siidddd",
        $data['orderId'],
        $data['userId'],
        $data['restaurantId'],
        $data['subtotal'],
        $data['shipping'],
        $data['tax'],
        $data['total']
    );

    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        throw new Exception("Failed to insert order: " . $stmt->error);
    }

    // Get the auto-increment ID if needed
    $orderDbId = $mysqli->insert_id;

    // 2. Insert each item into order_items table
    foreach ($data['items'] as $item) {
        // Log the item data for debugging
        error_log("Processing item: " . json_encode($item));

        // Try to get product ID - handle different scenarios
        $productId = null;

        // Case 1: Direct ID in the item
        if (isset($item['product_id']) && is_numeric($item['product_id'])) {
            $productId = $item['product_id'];
        }
        // Case 3: Need to look up by name
        else {
            // Make sure we have a name to search by
            if (!isset($item['name']) || empty($item['name'])) {
                throw new Exception("Item has no product_id or name: " . json_encode($item));
            }

            $productQuery = "SELECT product_id FROM products WHERE name = ? AND menu IN 
                 (SELECT menu_id FROM menus WHERE restaurant_id = ?)";
            $productStmt = $mysqli->prepare($productQuery);
            if (!$productStmt) {
                throw new Exception("Prepare failed for product query: " . $mysqli->error);
            }

            $productStmt->bind_param("si", $item['name'], $data['restaurantId']);
            $productStmt->execute();
            $productResult = $productStmt->get_result();

            if ($productResult->num_rows > 0) {
                $productRow = $productResult->fetch_assoc();
                $productId = $productRow['product_id'];
            } else {
                // Recuperiamo il menu corretto per il ristorante
                $restaurantRetriever = 'SELECT menu_id FROM menus WHERE restaurant_id = ?';
                $stmt = $mysqli->prepare($restaurantRetriever);
                $stmt->bind_param("i", $data['restaurantId']);
                $stmt->execute();
                $result = $stmt->get_result();
                $rowRes = $result->fetch_assoc();
                if (!$rowRes) {
                    throw new Exception("No menu found for restaurant_id: " . $data['restaurantId']);
                }

                // Creiamo il nuovo prodotto
                error_log("Product not found, creating temporary entry: " . $item['name']);
                $tempProductQuery = "INSERT INTO products (name, price, menu) VALUES (?, ?, ?)";
                $tempStmt = $mysqli->prepare($tempProductQuery);
                if (!$tempStmt) {
                    throw new Exception("Failed to prepare temp product insert: " . $mysqli->error);
                }

                // Puliamo il prezzo
                $priceValue = (float)preg_replace('/[^0-9.,]/', '', $item['price']);
                $priceValue = str_replace(',', '.', $priceValue);

                $tempStmt->bind_param("sdi", $item['name'], $priceValue, $rowRes["menu_id"]);
                $tempStmt->execute();

                if ($tempStmt->affected_rows <= 0) {
                    throw new Exception("Failed to create temporary product: " . $tempStmt->error);
                }

                $productId = $mysqli->insert_id; // Otteniamo l'ID appena creato
            }
        }

        if (!$productId) {
            throw new Exception("Could not determine product ID for item: " . json_encode($item));
        }

        // Handle price - different formats possible
        $price = 0;
        if (isset($item['itemTotal'])) {
            $price = $item['itemTotal'];
        } else if (isset($item['price'])) {
            // Parse the price (remove currency symbol and convert to float)
            $priceText = $item['price'];
            if (is_string($priceText)) {
                $price = (float)preg_replace('/[^0-9.,]/', '', $priceText);
                $price = str_replace(',', '.', $price);
            } else if (is_numeric($priceText)) {
                $price = $priceText;
            }
        }

        // Get quantity with fallback
        $quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? $item['quantity'] : 1;

        // Insert order item
        $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $itemStmt = $mysqli->prepare($itemQuery);
        if (!$itemStmt) {
            throw new Exception("Failed to prepare order item insert: " . $mysqli->error);
        }

        $itemStmt->bind_param("siid", $data['orderId'], $productId, $quantity, $price);
        $itemStmt->execute();

        if ($itemStmt->affected_rows <= 0) {
            throw new Exception("Failed to insert order item: " . $itemStmt->error);
        }
    }

    // Commit the transaction
    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Order saved successfully']);

} catch (Exception $e) {
    // Rollback the transaction on error
    $mysqli->rollback();

    // Log detailed error for server-side debugging
    error_log("Order error: " . $e->getMessage() . " - Data: " . json_encode($data));

    // Send error response
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}