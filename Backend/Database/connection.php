<?php
//Dati di connessione al database

require __DIR__ . "/../../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//componimento della stringa di connessione
$mysqli = new mysqli(
    $_ENV["DATABASE_HOSTNAME"],
    $_ENV["DATABASE_USERNAME"],
    "",
    $_ENV["DATABASE_NAME"]
);

if($mysqli->connect_errno){
    die("Errore di connessione al Database: " . $mysqli->connect_error);
}

return $mysqli;