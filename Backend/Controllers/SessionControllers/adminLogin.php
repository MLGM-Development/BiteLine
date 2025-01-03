<?php
$mysqli = require __DIR__ . "/../../../Backend/Database/connection.php";

//Controllo se l'utente ha un account
$sql = sprintf("SELECT * FROM admins WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

//Se la query non restituisce risultati, l'email non è valida o non ha un account
if($result->num_rows === 0) {
    echo "Email non trovata";
    exit();
}else{
    //Controllo se la password è corretta
    if(password_verify($_POST["password"], $user["password"])) {
        session_start();
        //Imposto la sessione
        $_SESSION["admin"] = $user["id"];

        $sessionId = bin2hex(random_bytes(32));
        $expireTime = time() + 2592000; //30 giorni di validità del cookie

        $sessionId_query = "UPDATE admins SET session_id = ?, cookie_expiry = ? WHERE admin_id = ?";
        $stmt = $mysqli->prepare($sessionId_query);
        $stmt->bind_param("sii", $sessionId, $expireTime, $user["id"]);
        $stmt->execute();

        setcookie("auth_token", $sessionId, $expireTime, "/");

        header("Location: ../../../Frontend/pages/admins/adminControls/adminPage.php");
        exit();
    }else{
        echo "Password errata";
        exit();
    }
}
