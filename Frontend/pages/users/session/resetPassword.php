<?php

$token = $_GET['token'];

$token_hash = hash('sha256', $token);

$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";

$isOwner = false;

$sql1 = "SELECT * FROM USERS WHERE reset_token_hash = ? AND reset_expiry > NOW()";
$stmt = $mysqli->prepare($sql1);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['user_id'];
} else {
    $sql2 = "SELECT * FROM OWNERS WHERE reset_token_hash = ? AND reset_expiry > NOW()";
    $stmt = $mysqli->prepare($sql2);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $isOwner = true;
        $userId = $row['owner_id'];
    } else {
        header("location: ../../errors/error-404.html");
        exit;
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $password = $_POST["password"];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($isOwner) {
        $sql = "UPDATE owners SET password = ?, reset_token_hash = NULL, reset_expiry = NULL WHERE owner_id = ?";
    } else {
        $sql = "UPDATE users SET password = ?, reset_token_hash = NULL, reset_expiry = NULL WHERE user_id = ?";
    }

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("location: auth-login.php");
        exit;
    } else {
        echo "Errore durante il reimpostazione della password.";
    }
}
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../../../assets/css/FormStyle.css">
    <title>Reimposta password | BiteLine</title>
</head>
<body>
<div class="mega-container">
    <!-- Animated background -->
    <div class="animated-background">
        <div class="animated-blob blob-1"></div>
        <div class="animated-blob blob-2"></div>
        <div class="animated-blob blob-3"></div>
    </div>

    <div class="container">
        <div class="contact-section glass-morphism">
            <div class="glow2"></div>
            <div class="grid-dots"></div>
            <div class="section-content">
                <span class="badge fade-in-up">BiteLine</span>
                <h1 class="fade-in-up">Reimposta password</h1>
                <p class="fade-in-up stagger-delay-1">Inserisci i dati richiesti</p>

                <form class="contact-form fade-in-up stagger-delay-2" id="loginForm" method="post">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <input type="password" id="password" placeholder=" " name="password" required>
                        <label for="password">Password</label>
                    </div>

                    <button type="submit">Accedi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../../assets/script/formScript.js"></script>

</body>
</html>
