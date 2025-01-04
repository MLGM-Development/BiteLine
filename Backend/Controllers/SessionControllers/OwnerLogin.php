<?php
    $mysqli = require __DIR__ . "/../../Database/connection.php";

    $sql = sprintf("SELECT * FROM owners WHERE mail = '%s'", $mysqli->real_escape_string($_POST["email"]));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if($result->num_rows === 0) {
        echo "Email non trovata";
        exit();
    } else {
        if (password_verify($_POST["password"], $user["password"])) {
            session_start();
            $_SESSION["owner"] = $user["owner_id"];

            $sessionId = bin2hex(random_bytes(32));
            $expireTime = time() + 2592000;

            $sessionId_query = "UPDATE owners SET session_token = ?, cookie_expiry = ? WHERE owner_id = ?";
            $stmt = $mysqli->prepare($sessionId_query);
            $stmt->bind_param("sii", $sessionId, $expireTime, $user["owner_id"]);
            $stmt->execute();

            setcookie("auth_token", $sessionId, $expireTime, "/");

            header("Location: ../../../Frontend/pages/users/Owners/ownersControls/ownerPage.php");
            exit();
        } else {
            echo "Password errata";
            exit();
        }
    }

