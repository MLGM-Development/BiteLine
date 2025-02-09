<?php
function getJwtToken() {
    if (isset($_COOKIE['auth_token'])) {
        return $_COOKIE['auth_token'];
    }
    return null;
}

// Function to verify JWT token with Node.js server and extract payload using cURL
function verifyJwtToken($jwtToken) {
    $url = 'http://localhost:3000/node-api/protected';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($jwtToken)
    ]);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Error verifying token: ' . curl_error($ch));
    }
    curl_close($ch);

    // Per debug, mostra la risposta ottenuta
    //echo "Risposta dal server Node: " . $result; exit;

    $data = json_decode($result, true);
    return $data;
}