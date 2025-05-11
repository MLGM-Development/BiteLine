<?php
$mysqli = require __DIR__ . "/../Database/connection.php";

$isHirable = $_POST['hire'];
$person = $_POST['person'];

if ($isHirable) {
    $sql = "UPDATE employees_history SET start_date = NOW(), end_date = NULL WHERE employee_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $person);
    $stmt->execute();
}

$sql = "SELECT users.email, restaurants.name, restaurants.email as res, users.name, users.l_name FROM employees_history, users, restaurants
        WHERE employees_history.employee_id = ? 
          AND employees_history.employee_id = users.user_id 
          AND employees_history.restaurant_id = restaurants.restaurant_id";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $person);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$email = $row['email'];
$restaurantName = $row['name'];
$restaurantMail = $row['res'];
$nome = $row['name'];
$cognome = $row['l_name'];

$mail = require __DIR__ . "/../sender/mailer.php";
$mail->setFrom('bitelineservices@gmail.com');
$mail->addAddress($email);
$mail->Subject = "Assunzione da parte di $restaurantName";
$mail->isHTML(true);
$mail->Body = <<<END
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulazioni - Sei stato assunto!</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0D1117;
            color: #E6EDF3;
            line-height: 1.6;
        }
        
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: linear-gradient(135deg, #121824 0%, #1A1F2C 100%);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(99, 116, 195, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        
        .header {
            padding: 30px;
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 60%);
            pointer-events: none;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.5px;
        }
        
        .title {
            font-size: 36px;
            font-weight: 700;
            margin: 20px 0;
            background: linear-gradient(to right, #FFFFFF, #E2E2E2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        .subtitle {
            font-size: 18px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
        }
        
        .content {
            padding: 40px 30px;
            position: relative;
        }
        
        .message {
            background: rgba(30, 36, 50, 0.8);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(99, 116, 195, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .message p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #B4BCD0;
        }
        
        .message p:last-child {
            margin-bottom: 0;
        }
        
        .highlight {
            color: #61DAFB;
            font-weight: 500;
        }
        
        .details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
        }
        
        .detail-item {
            flex: 1;
            min-width: 200px;
            background: rgba(22, 27, 39, 0.8);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(99, 116, 195, 0.1);
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-color: rgba(99, 116, 195, 0.3);
        }
        
        .detail-icon {
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .detail-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .detail-text {
            font-size: 14px;
            color: #8B95A9;
        }
        
        .cta {
            text-align: center;
            margin: 40px 0 20px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            color: #fff;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(71, 118, 230, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(71, 118, 230, 0.4);
        }
        
        .steps {
            margin: 40px 0;
        }
        
        .step {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .step-desc {
            font-size: 14px;
            color: #8B95A9;
        }
        
        .footer {
            text-align: center;
            padding: 30px;
            background: rgba(16, 20, 29, 0.8);
            border-top: 1px solid rgba(99, 116, 195, 0.1);
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 15px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(30, 36, 50, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-link:hover {
            transform: translateY(-3px);
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
        }
        
        .footer-text {
            font-size: 14px;
            color: #5A6376;
            margin-top: 20px;
        }
        
        .animated-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 1;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(71, 118, 230, 0.1) 0%, rgba(142, 84, 233, 0.1) 100%);
        }
        
        .circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -100px;
        }
        
        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -80px;
            left: -80px;
        }
        
        .dots {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.2;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 0.5;
            }
        }
        
        .pulse {
            animation: pulse 4s infinite ease-in-out;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                border-radius: 16px;
            }
            
            .title {
                font-size: 28px;
            }
            
            .subtitle {
                font-size: 16px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .message {
                padding: 20px;
            }
            
            .details {
                flex-direction: column;
            }
            
            .detail-item {
                min-width: unset;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="message">
                <p>Gentile $nome $cognome,</p>
                <p>È con grande piacere che ti diamo il benvenuto ufficiale. Dopo un processo di selezione rigoroso, siamo rimasti colpiti dalle tue competenze, dalla tua esperienza e soprattutto dal tuo potenziale.</p>
                <p>Ti confermiamo che la nostra offerta per la posizione è stata finalizzata. Il tuo viaggio con noi inizierà ufficialmente</p>
                <p>Nelle prossime settimane, riceverai tutte le informazioni necessarie per prepararti al tuo primo giorno, compresi i dettagli sul processo di onboarding e sull'orientamento iniziale.</p>
            </div>
            
            <div class="details">
                <div class="detail-item">
                    <h3 class="detail-title">Data di Inizio</h3>
                    <p class="detail-text">Da definire con l'HR</p>
                </div>
                <div class="detail-item">
                    <h3 class="detail-title">Contatto HR</h3>
                    <p class="detail-text">$restaurantMail</p>
                </div>
                <div class="detail-item">
                    <h3 class="detail-title">Posizione</h3>
                    <p class="detail-text">Concordata nella candidatura</p>
                </div>
            </div>
            
            <div class="steps">
                <h3 style="margin-bottom: 20px; color: #fff; font-size: 20px;">Prossimi Passi:</h3>
                <div class="step">
                    <div class="step-content">
                        <h4 class="step-title">Mettiti in contatto con il ristorante</h4>
                        <p class="step-desc">Scrivi una mail a $restaurantMail per scoprire i prossimi passaggi</p>
                    </div>
                </div>
            </div>   
        </div>
        
        <div class="footer">
            <p style="color: #fff; font-size: 16px;">BiteLine.</p>
            <p class="footer-text">© 2025 BiteLine | MLGM Development. Tutti i diritti riservati.</p>
        </div>
    </div>
</body>
</html>
END;

try {
    $mail->send();
}catch (Exception $e) {
    echo "Errore durante l'invio della mail: {$mail->ErrorInfo}";
    exit;
}

header("Location: ../../Frontend/pages/users/Owners/dashboard/application-sent.php");

