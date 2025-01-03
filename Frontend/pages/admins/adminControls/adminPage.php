<?php
//Dati di connessione al database
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";

if (!isset($_COOKIE['auth_token'])) {
    //Se non c'è il cookie, reindirizza alla pagina di login
    header("Location: ../logAdmin.html");
    exit();
}else{
    $session_code = $mysqli->real_escape_string($_COOKIE['auth_token']); //Query per selezionare l'admin

    $verify_query = "SELECT * FROM admins WHERE session_id = ?"; //Query per selezionare l'admin
    $stmt = $mysqli->prepare($verify_query); //Preparazione della query
    $stmt->bind_param("s", $session_code); //Binding dei parametri
    $stmt->execute();
    $result = $stmt->get_result(); //Esecuzione della query

    if ($result->num_rows < 1) {
        //Se non ci sono risultati, il cookie non è valido
        header("Location: ../logAdmin.html");

    }else{
        $admin = $result->fetch_assoc(); //Fetch dell'admin
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Ciao</h1>
</body>
</html>
