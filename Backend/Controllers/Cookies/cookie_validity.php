<?php

//Funzione per determinare la validità del cookie (per tenersi loggato)
function isTokenValid($token)
{
    $mysqli = require __DIR__ . "/../../Database/connection.php";

    $cookie_query = "SELECT * FROM admins WHERE session_id = ? AND cookie_expiry > ?"; //Query per selezionare il cookie
    $stmt = $mysqli->prepare($cookie_query); //Preparazione della query

    $current_time = time(); //Tempo attuale

    $stmt->bind_param("si", $token, $current_time); //Binding dei parametri
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows === 0) {
        //IMPORTANTE, INSERIRE IL SISTEMA DI COOKIE ANCHE PER I LAVORATORI DEL RISORANTE

        return false; //Se non ci sono risultati, il cookie non è valido
    }else{
        return true; //Se ci sono risultati, il cookie è valido
    }
}

if(!isset($_COOKIE["auth_token"])) {
    //Se non c'è il cookie, reindirizza alla pagina di login
    header("Location: ../../../Frontend/pages/logAdmin.html");
    exit();
}

$SessionId = $_COOKIE["auth_token"];
if (!isTokenValid($SessionId)) {
    //Se il cookie non è valido, reindirizza alla pagina di login
    header("Location: ../../../Frontend/pages/logAdmin.html");
    exit();
}