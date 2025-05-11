<?php
$mysqli = require __DIR__ . "/../Database/connection.php";


$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$tokeHash = hash('sha256', $token);
$expiry = date("Y-m-d H:i:s", time()+60*30); //Token valido solo per 30 minuti

$sql = "UPDATE users SET reset_token_hash = ?, reset_expiry = ? WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $tokeHash, $expiry, $email);
$stmt->execute();

if($stmt->affected_rows == 0){
    $sql = "UPDATE owners SET reset_token_hash = ?, reset_expiry = ? WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $tokeHash, $expiry, $email);
    $stmt->execute();

    if($stmt->affected_rows == 0){
        header("location: ../../Frontend/pages/errors/error-404.html");
    }
}

if($stmt->affected_rows){
    $mail = require __DIR__ . "/mailer.php";
    $mail->setFrom('bitelineservices@gmail.com');
    $mail->addAddress($email);
    $mail->Subject = 'Reset della password di BiteLine';
    $mail->Body = <<<END
    <!DOCTYPE html>
<html>
    <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: #121212;
      color: #e0e0e0;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .logo {
      width: 120px;
      height: 120px;
      background-color: #303030;
      border-radius: 50%;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .logo-text {
      font-size: 24px;
      font-weight: bold;
      color: #bb86fc;
    }
    .card {
      background-color: #1e1e1e;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    h1 {
      color: #ffffff;
      font-size: 24px;
      margin-top: 0;
      margin-bottom: 20px;
      font-weight: 600;
    }
    p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 20px;
      color: #b0b0b0;
    }
    .btn {
      display: inline-block;
      background-color: #bb86fc;
      color: #121212;
      padding: 14px 30px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      margin: 20px 0;
      text-align: center;
    }
    .divider {
      height: 1px;
      background-color: #303030;
      margin: 30px 0;
    }
    .footer {
      text-align: center;
      font-size: 14px;
      color: #757575;
      margin-top: 30px;
    }
    .code-container {
      background-color: #303030;
      border-radius: 6px;
      padding: 15px;
      text-align: center;
      margin: 20px 0;
    }
    .code {
      font-family: monospace;
      font-size: 24px;
      letter-spacing: 4px;
      color: #ffffff;
    }
    @media only screen and (max-width: 480px) {
      .container {
        padding: 20px 15px;
      }
      .card {
        padding: 20px;
      }
    }
  </style>
    </head>
    <body>
  <div class="container">

    
    <div class="card">
      <h1>Reimposta la tua password</h1>
      <p>Abbiamo ricevuto una richiesta di reset della tua password di BiteLine. Usa il pulsante sottostante per creare una nuova password, il link è valido per soli 30 minuti.</p>
      
      <a href="http://localhost/BiteLine/Frontend/pages/users/session/resetPassword.php?token=$token" class="btn">Resetta Password</a>
      
      <p>Se non hai richiesto tu questo reset, puoi tranquillamente ignorare questa mail</p>
      
      <p>Se hai qualsiasi domanda o perplessità rivolgiti al nostro team di supporto</p>
    </div>
    
    <div class="footer">
      <p>© 2025 BiteLine. Tutti i diritti riservarti.</p>
    </div>
  </div>
    </body>
</html>
END;

    try {
        $mail->send();
        header("location: ../../Frontend/pages/users/session/resetPasswordConfirm.html");
    }catch (Exception $e) {
        echo "Errore nell'invio della mail: {$mail->ErrorInfo}";
    }
}