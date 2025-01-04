<?php
$mysqli = require __DIR__ . "/../../Database/connection.php";

//Array con tutti i campi richiesti e obbligatori
$required = [
    "name" => "Nome",
    "surname" => "Cognome",
    "password" => "Password",
    "email" => "Email"
];

//Funzione utilizzata per lanciare un errore del form
function throwFormError($message) {
    echo $message;
    exit();
}

//Controlla se tutti i campi richiesti siano stati compilati (Controllo backend)
foreach ($required as $key => $value) {
    if (!isset($_POST[$key])) {
        die("Il campo $value è obbligatorio");
    }
}

//Controllo della validità dell'email che si aggancia alla funzione per ilo lancio degli errori
if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    throwFormError("Email non valida");
}

//Controllo della validità della password che si aggancia alla funzione per il lancio degli errori
if(strlen($_POST["password"]) < 8) {
    throwFormError("La password deve essere di almeno 8 caratteri");
}

//Controllo della validità del nome che si aggancia alla funzione per il lancio degli errori
if(strlen($_POST["name"]) < 2) {
    throwFormError("Il nome deve essere di almeno 2 caratteri");
}

//Controllo della validità del cognome che si aggancia alla funzione per il lancio degli errori
if(strlen($_POST["surname"]) < 2) {
    throwFormError("Il cognome deve essere di almeno 2 caratteri");
}

//Funzione per cryptare la password dell'admin
$hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$query = $mysqli->prepare("INSERT INTO owners (name, l_name, mail, password) VALUES (?, ?, ?, ?)");
$query->bind_param("ssss", $_POST["name"], $_POST["surname"], $_POST["email"], $hash);
$query->execute();
$query->close();

header("Location: ../../../Frontend/pages/users/Owners/logOwner.html");