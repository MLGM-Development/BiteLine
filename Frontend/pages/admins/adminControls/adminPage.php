<?php
//Dati di connessione al database
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";

$session_query = $mysqli->real_escape_string($_COOKIE['auth_token']) //Query per selezionare l'admin
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
