<?php
$mail = require __DIR__ . "/mailer.php";

$source=$_POST["email"];
$subject=$_POST["subject"];
$service=$_POST["service"];
$message=$_POST["message"];
$nome=$_POST["name"];

$mail->setFrom($source, $nome);
$mail->addAddress('bitelineservices@gmail.com');
$mail->Subject = $service . $subject;
$mail->Body = $message;

try {
    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    exit;
}