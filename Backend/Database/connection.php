<?php
//Dati di connessione al database
$host = "localhost";
$dbname = "biteline_db";
$username = "root";
$password = "";

//componimento della stringa di connessione
$mysqli = new mysqli($host, $username, $password, $dbname);

if($mysqli->connect_errno){
    die("Errore di connessione al Database: " . $mysqli->connect_error);
}

return $mysqli;