<?php
$mysqli = require __DIR__ . "/../../Backend/Database/connection.php";

$name = $_POST["nplate"];
$menu = $_POST["menu_id"];
$description = $_POST["dplate"];
$category = $_POST["dd-input"];
$price = $_POST["price"];

if(strlen($name) == 0 || strlen($description) == 0 || strlen($category) == 0 || strlen($price) == 0 || strlen($menu) == 0) {
    die("Errore: uno o più campi sono vuoti");
}

$SQL = "INSERT INTO products (name, description, category, price, menu) VALUES (?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($SQL);
$stmt->bind_param("sssii", $name, $description, $category, $price, $menu);
$stmt->execute();