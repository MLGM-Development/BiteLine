<?php
    $mysqli = require __DIR__ . "/../../../../../Backend/Database/connection.php";

    if(isset($_COOKIE['auth_token'])) {
        $session_code = $mysqli->real_escape_string($_COOKIE['auth_token']);

        $verify_query = "SELECT * FROM owners WHERE session_id = ?";
        $stmt = $mysqli->prepare($verify_query);
        $stmt->bind_param("s", $session_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {
            header("Location: ../logOwner.html");
        } else {
            $owner = $result->fetch_assoc();
        }
    } else {
        header("Location: ../logOwner.html");
        exit();
    }
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php
        $sqlVerifyRestaurant = "SELECT * FROM restaurants WHERE owner = ?";
        $stmt = $mysqli->prepare($sqlVerifyRestaurant);
        $stmt->bind_param("i", $owner["owner_id"]);
        $stmt->execute();

        $result = $stmt->get_result();
    ?>

    <h1>Benvenuto <?php echo $owner["name"] ?></h1>
    <h2>Ristoranti</h2>
    <?php
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<h3>" . $row["name"] . "</h3>";
            }
        } else {
            echo "<h3>Non hai alcun ristorante registrato</h3>";
            echo "<a href='../../../location/managing/RegRestaurant.php'>Registra il tuo ristorante</a>";
        }
    ?>

</body>
</html>
