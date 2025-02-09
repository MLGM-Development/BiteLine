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

    if ($payload['role'] !== 'owner'){
        header('Location: ../../../errors/error-403.html');
    }

    $ownerId = $payload['id'];
} else {
    header('Location: ../../../errors/error-500.html');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "INSERT INTO restaurants (name, address, email, phone_number, owner) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssi", $_POST["resName"], $_POST["address"], $_POST["resMail"], $_POST["resPhone"], $payload['id']);
    $stmt->execute();

    header("Location: ../../users/Owners/ownersControls/ownerPage.php");
    exit();
}
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Title</title>
</head>
<body>
<h1>Registrazione del ristorante</h1>
    <form method="post">
        <label for="resName">Nome del Ristorante</label>
        <input type="text" name="resName" id="resName" required>

        <label for="address">Indirizzo del ristorante</label>
        <input type="text" name="address" id="address" required>

        <label for="resMail">Mail del ristorante</label>
        <input type="text" name="resMail" id="resMail">

        <label for="resPhone">N. di telefono del ristorante</label>
        <input type="text" name="resPhone" id="resPhone">

        <input type="submit" value="Registra">
    </form>
</body>
</html>