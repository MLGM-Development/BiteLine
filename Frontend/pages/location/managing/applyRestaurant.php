<?php
$mysqli = require __DIR__ . "/../../../../Backend/Database/connection.php";
require __DIR__ . '/../../../../Backend/Controllers/Cookies/JWT_Verifier.php';

$jwtToken = getJwtToken();

if ($jwtToken) {
    // Verify JWT token and extract payload
    $payload = verifyJwtToken($jwtToken);

    if (!isset($payload['id'])) {
        die('User not authenticated');
    }

    if ($payload['role'] == 'owner'){
        header('Location: ../../../errors/error-403.html');
    }

    $ownerId = $payload['id'];
} else {
    header('Location: ../../session/auth-login.html');
}

$uploadDir = __DIR__ . '/../../../assets/documents/applications/';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $restaurantName = $_POST["resName"];
    $description = $_POST["description"];
    $address = $_POST["address"];
    $email = $_POST["resMail"];
    $phone = $_POST["resPhone"];
    $cuisine = $_POST["cuisine"];

    // Verifica che la cartella esista, altrimenti creala
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Gestione caricamento immagine
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileType = $_FILES['image']['type'];
        $fileSize = $_FILES['image']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

        if (in_array($fileExtension, $allowedTypes)) {
            // Genera un nome sicuro per il file
            $safeRestaurantName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $restaurantName);
            $newFileName = $safeRestaurantName . '_' . time() . '.' . $fileExtension;
            $targetFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $imagePath = $newFileName;
            } else {
                die("Errore nel caricamento dell'immagine.");
            }
        } else {
            die("Formato file non supportato. Formati validi: " . implode(", ", $allowedTypes));
        }
    }

    // Inserisci i dati nel database
    $sql = "INSERT INTO restaurants (image, name, description, address, email, phone_number, cuisine, owner) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $mysqli->error);
    }

    $stmt->bind_param("sssssssi", $imagePath, $restaurantName, $description, $address, $email, $phone, $cuisine, $payload['id']);

    if ($stmt->execute()) {
        header("Location: ../../users/Owners/dashboard/ownIndexDash.php");
        exit();
    } else {
        die("Errore nell'inserimento nel database: " . $stmt->error);
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
    <title>Invia candidatura | BiteLine</title>
</head>
<body>

</body>
</html>
