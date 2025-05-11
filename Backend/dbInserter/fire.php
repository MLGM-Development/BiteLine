<?php
$mysqli = require __DIR__ . "/../Database/connection.php";

$isHirable = $_POST['fire'];
$person = $_POST['person'];

if ($isHirable) {
    $sql = "UPDATE employees_history SET start_date = NULL, end_date = NOW() WHERE employee_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $person);
    $stmt->execute();
}

header("Location: ../../Frontend/pages/users/Owners/dashboard/employees.php");

