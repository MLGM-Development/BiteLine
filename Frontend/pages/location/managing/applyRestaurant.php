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

    $userId = $payload['id'];
} else {
    header('Location: ../../session/auth-login.html');
}

$uploadDir = __DIR__ . '/../../../assets/documents/applications/';
$restaurantId = $_GET['restaurant_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $role = $_POST["role"];

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
            $safeRestaurantName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $restaurantId);
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
    $sql = "INSERT INTO employees_history (cv_path, employee_id, restaurant_id, role) 
            VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $mysqli->error);
    }

    $stmt->bind_param("siis", $imagePath, $payload['id'], $restaurantId, $role);

    if ($stmt->execute()) {
        header("Location: ../../users/Customers/dataConfirmation.html");
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
    <link rel="shortcut icon" href="../../../assets/media/images/favicon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../assets/css/FormStyle.css">
    <title>Invia candidatura | BiteLine</title>
</head>
<body>
<div class="mega-container">
    <!-- Animated background -->
    <div class="animated-background">
        <div class="animated-blob blob-1"></div>
        <div class="animated-blob blob-2"></div>
        <div class="animated-blob blob-3"></div>
    </div>

    <!-- Success message -->
    <div class="success-message" id="successMessage">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div>
            <strong>Messaggio inviato con successo</strong>
            <p>Ti ricontatteremo presto.</p>
        </div>
    </div>

    <div class="container">


        <div class="contact-section glass-morphism">
            <div class="glow2"></div>
            <div class="grid-dots"></div>
            <div class="section-content">
                <span class="badge fade-in-up">invio</span>
                <h1 class="fade-in-up">Candidature</h1>
                <p class="fade-in-up stagger-delay-1">Inserisci i dati richiesti relativi alla tua vita professionale</p>

                <form class="contact-form fade-in-up stagger-delay-2" id="contactForm" enctype="multipart/form-data" action="" method="post">

                    <div class="form-group image-upload-container">
                        <div id="imageUploadArea" class="image-upload-area" tabindex="0">
                            <input type="file" id="imageUpload" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" class="file-input" name="image" hidden required>
                            <div id="uploadPlaceholder" class="upload-placeholder">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <p>Trascina qui il tuo curriculum</p>
                                <span>Formati supportati: JPG, PNG, GIF, PDF, DOC, DOCX</span>
                            </div>
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" src="" alt="Preview">
                                <button id="removeImage" class="remove-image" type="button">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <label for="imageUpload">Curriculum</label>
                    </div>
                    <?php
                    $sql = "SELECT * FROM users WHERE user_id = ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $payload['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    echo "<div class=\"form-row\">
                        <div class=\"form-group\">
                            <input type=\"text\" id=\"name\" placeholder=\" \" name=\"name\" value=\"".$row['name']."\" required>
                            <label for=\"name\">Nome</label>
                        </div>
                        <div class=\"form-group\">
                            <input type=\"text\" id=\"surname\" placeholder=\" \" name=\"surname\" value=\"".$row['l_name']."\" required>
                            <label for=\"surname\">Cognome</label>
                        </div>
                    </div>
                    <div class=\"form-group\">
                            <input type=\"email\" id=\"email\" placeholder=\" \" name=\"resMail\" value=\"".$row['email']."\" required>
                            <label for=\"email\">Email</label>
                        </div>
                        
                        ";

                    ?>

                    <div class="form-group">
                        <select id="role" name="role" required>
                            <option value="cuoco">Cuoco</option>
                            <option value="aiuto_cuoco">Aiuto cuoco</option>
                            <option value="cameriere">Cameriere</option>
                        </select>
                        <label for="role">Posizione lavorativa</label>
                    </div>

                    <div class="form-group">
                        <input type="text" id="phone" placeholder=" " name="resPhone" required>
                        <label for="phone">N. Telefono</label>
                    </div>

                    <button type="submit">Invia</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../../assets/script/formScript.js"></script>
</body>
</html>
