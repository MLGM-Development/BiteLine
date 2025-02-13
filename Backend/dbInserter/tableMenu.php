<?php
$mysqli = require __DIR__ . "/../Database/connection.php";

$name = $_POST["nplate"];
$menu = $_POST["menu_id"];
$description = $_POST["dplate"];
$category = $_POST["dd-input"];
$price = $_POST["price"];


if(strlen($name) == 0 || strlen($description) == 0 || strlen($category) == 0 || strlen($price) == 0 || strlen($menu) == 0) {
    die("Errore: uno o più campi sono vuoti");
}

if (isset($_FILES["filepond"]) && $_FILES['filepond']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['filepond']['tmp_name'];
    $fileName = $_FILES['filepond']['name'];
    $fileSize = $_FILES['filepond']['size'];
    $fileType = $_FILES['filepond']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Directory in which the uploaded file will be moved
        $uploadFileDir = __DIR__ . '/../../Frontend/assets/media/images/users/menu/';

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Insert into database
            $insertQuery = "INSERT INTO products (name, description, price, category, menu, image_path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("ssdsis", $name, $description, $price, $category, $menu, $newFileName);
            $stmt->execute();

            header("Location: ../../Frontend/pages/users/Owners/dashboard/table-menu-creator.php");
            exit();
        } else {
            echo 'There was an error moving the uploaded file.';
        }
    } else {
        echo 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
} else {
    echo 'There was an error uploading the file.';
}

/*$SQL = "INSERT INTO products (name, description, category, price, menu) VALUES (?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($SQL);
$stmt->bind_param("sssii", $name, $description, $category, $price, $menu);
$stmt->execute();*/