<?php
$mysqli = require __DIR__ . "/../../../../../Backend/Database/connection.php";
require __DIR__ . '/../../../../../Backend/Controllers/Cookies/JWT_Verifier.php';

$sql = "SELECT name, description, price 
            FROM products";

$result = $mysqli->query($sql);

$jwtToken = getJwtToken();

if ($jwtToken) {
    // Verify JWT token and extract payload
    $payload = verifyJwtToken($jwtToken);

    if (!isset($payload['id'])) {
        die('User not authenticated');
    }

    if ($payload['role'] !== 'owner'){
        header('Location: ../../errors/error-403.html');
    }

    $ownerId = $payload['id'];
} else {
    header('Location: ../session/auth-login.html');
}

$restaurantRetriever = "SELECT * FROM restaurants WHERE owner = ?";
$stmt = $mysqli->prepare($restaurantRetriever);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$restResult = $stmt->get_result();
$restRow = $restResult->fetch_assoc();

$menuRetriever = "SELECT * FROM menus WHERE restaurant_id = ? AND menus.name = 'asporto'";
$stmt = $mysqli->prepare($menuRetriever);
$stmt->bind_param("i", $restRow["restaurant_id"]);
$stmt->execute();

$stmtResult = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    // Retrieve the image path
    $menuPhotoRetriever = "SELECT image_path FROM products WHERE product_id = ?";
    $stmt = $mysqli->prepare($menuPhotoRetriever);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $imagePath = $result->fetch_assoc()['image_path'];
        $stmt->close();

        // Delete the product
        $deleteQuery = "DELETE FROM products WHERE product_id = ?";
        $stmt = $mysqli->prepare($deleteQuery);
        $stmt->bind_param("i", $deleteId);
        $stmt->execute();
        $stmt->close();

        // Delete the image file
        $file = "../../../../assets/media/images/users/menu/" . $imagePath;
        if (file_exists($file)) {
            if (unlink($file)) {
                echo "File deleted successfully";
            } else {
                echo "File not deleted";
            }
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?action=deleted");
        exit();
    } else {
        echo "Error retrieving image path.";
    }
}


?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea il tuo menu d'asporto | BiteLine</title>



    <link rel="shortcut icon" href="../../../../assets/media/images/favicon/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../../assets/bootstrap/extensions/filepond/filepond.css">
    <link rel="stylesheet" href="../../../../assets/bootstrap/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.css">
    <link rel="stylesheet" href="../../../../assets/bootstrap/extensions/toastify-js/src/toastify.css">

    <link rel="stylesheet" href="../../../../assets/bootstrap/extensions/sweetalert2/sweetalert2.min.css">

  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/application-email.css">
  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/app.css">
  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/app-dark.css">
</head>

<body>
    <script src="../../../../assets/bootstrap/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">
                <a href="../../../index.html"><img src="../../../../assets/bootstrap/compiled/svg/LogoBiteLine.png" alt="Logo" srcset=""></a>
            </div>
            <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--system-uicons" width="20" height="20"
                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                            opacity=".3"></path>
                        <g transform="translate(-210 -1)">
                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                            <circle cx="220.5" cy="11.5" r="4"></circle>
                            <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                        </g>
                    </g>
                </svg>
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                    viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                    </path>
                </svg>
            </div>
            <div class="sidebar-toggler  x">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>

            <li
                    class="sidebar-item">
                <a href="ownIndexDash.php" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>


            </li>

            <li
                    class="sidebar-item ">
                <a href="table-datatable.php" class='sidebar-link'>
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    <span>Ordini</span>
                </a>


            </li>

            <li
                    class="sidebar-item  has-sub active">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-collection-fill"></i>
                    <span>Crea Menu</span>
                </a>

                <ul class="submenu ">

                    <li class="submenu-item ">
                        <a href="table-menu-creator.php" class="submenu-link">Menu Tavolo</a>

                    </li>

                    <li class="submenu-item active">
                        <a href="deliver-menu-creator.php" class="submenu-link">Asporto/Consegna</a>

                    </li>
                </ul>


            </li>

            <li
                    class="sidebar-item  ">
                <a href="application-sent.php" class='sidebar-link'>
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Candidature</span>
                </a>


            </li>

            <li
                    class="sidebar-item">
                <a href="employees.php" class='sidebar-link'>
                    <i class="bi bi-people-fill"></i>
                    <span>Dipendenti</span>
                </a>


            </li>

        </ul>
    </div>
</div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
<div class="page-heading overflow-hidden">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Crea il tuo menu</h3>
                <p class="text-subtitle text-muted">Utilizza questo form per inserire gli alimenti del tuo menu da tavolo.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ownIndexDash.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Crea Menu</li>
                        <li class="breadcrumb-item active" aria-current="page">Menu asporto/consegna</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Inserisci un alimento</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-vertical" method="post" action="../../../../../Backend/dbInserter/tableMenu.php" enctype="multipart/form-data">
                        <?php
                            if ($stmtResult && $stmtResult->num_rows <= 0) {
                                $available = 1;
                                $menuType = "asporto";
                                $menuCreator = "INSERT INTO menus (restaurant_id, name, is_available) VALUES (?, ?, ?)";
                                $stmt = $mysqli->prepare($menuCreator);
                                $stmt->bind_param("isi", $restRow["restaurant_id"], $menuType, $available);
                                $stmt->execute();

                                header("Location: " . $_SERVER['PHP_SELF']);
                            } else {
                                $stmtRow = $stmtResult->fetch_assoc();
                                echo "
                                    <div class=\"form-body\">
                            <div class=\"row\">
                                <div class=\"col-12\">
                                    <div class=\"form-group\">
                                        <label for=\"first-name-vertical\">Nome piatto</label>
                                        <input type=\"text\" id=\"first-name-vertical\" class=\"form-control\"
                                               name=\"nplate\" placeholder=\"Bistecca di ...\" required>
                                       <input type=\"hidden\" value='" . $stmtRow["menu_id"] . "' name=\"menu_id\">
                                    </div>
                                </div>
                                <div class=\"col-12\">
                                    <div class=\"form-group\">
                                        <label for=\"email-id-vertical\">Breve descrizione</label>
                                        <input type=\"text\" id=\"email-id-vertical\" class=\"form-control\"
                                               name=\"dplate\" placeholder=\"Bistecca di ottima carne di ...\" required>
                                    </div>
                                </div>
                                <div class=\"col-12\">
                                    <div class=\"form-group\">
                                        <label for=\"contact-info-vertical\">Prezzo</label>
                                        <input type=\"number\" id=\"contact-info-vertical\" class=\"form-control\"
                                               name=\"price\" placeholder=\"36,99\" required>
                                    </div>
                                </div>
                                <div class=\"col-12\">
                                    <div class=\"form-group\">
                                        <label for=\"dd-input\">Categoria</label>
                                        <input list=\"plateCategory\" id=\"dd-input\" name=\"dd-input\" class=\"form-control\"
                                               placeholder=\"Categoria del piatto\" required>
                                        <datalist id=\"plateCategory\">
                                            <option value=\"Antipasti\">
                                            <option value=\"Primi piatti\">
                                            <option value=\"Secondi piatti\">
                                            <option value=\"Pizze\">
                                            <option value=\"Contorni\">
                                            <option value=\"Dolci\">
                                            <option value=\"Bevande\">
                                        </datalist>
                                    </div>
                                </div>
                                <div class=\"col-12\">
                                    <div class=\"form-group\">
                                        <label for=\"dd-input\">Immagine</label>
                                        <div class=\"filepond--root image-filter-filepond filepond--hopper\" data-style-button-remove-item-position=\"left\" data-style-button-process-item-position=\"right\" data-style-load-indicator-position=\"right\" data-style-progress-indicator-position=\"right\" data-style-button-remove-item-align=\"false\" style=\"height: 76px;\">
                                            <input class=\"filepond--browser\" type=\"file\" id=\"filepond--browser-vup0l3j4c\" aria-controls=\"filepond--assistant-vup0l3j4c\" aria-labelledby=\"filepond--drop-label-vup0l3j4c\" accept=\"image/png,image/jpg,image/jpeg\" name=\"filepond\">
                                            <div class=\"filepond--drop-label\" style=\"transform: translate3d(0px, 0px, 0px); opacity: 1;\">
                                            <label for=\"filepond--browser-vup0l3j4c\" id=\"filepond--drop-label-vup0l3j4c\" aria-hidden=\"true\">S &amp; Trascina i tuoi file o <span class=\"filepond--label-action\" tabindex=\"0\">Sfoglia</span></label></div><div class=\"filepond--list-scroller\" style=\"transform: translate3d(0px, 0px, 0px);color: #0d0d0d;\">
                                            <ul class=\"filepond--list\" role=\"list\"></ul>
                                        </div>
                                            <div class=\"filepond--panel filepond--panel-root\" data-scalable=\"true\">
                                                <div class=\"filepond--panel-top filepond--panel-root\"></div>
                                                <div class=\"filepond--panel-center filepond--panel-root\" style=\"transform: translate3d(0px, 8px, 0px) scale3d(1, 0.6, 1);\"></div>
                                                <div class=\"filepond--panel-bottom filepond--panel-root\" style=\"transform: translate3d(0px, 68px, 0px);\"></div>
                                            </div>
                                            <span class=\"filepond--assistant\" id=\"filepond--assistant-vup0l3j4c\" role=\"status\" aria-live=\"polite\" aria-relevant=\"additions\"></span>
                                            <fieldset class=\"filepond--data\"></fieldset>
                                            <div class=\"filepond--drip\"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"col-12 d-flex justify-content-end\">
                                    <button type=\"submit\" class=\"btn btn-primary me-1 mb-1\">Invia</button>
                                    <button type=\"reset\"
                                            class=\"btn btn-light-secondary me-1 mb-1\">Reset</button>
                                </div>
                            </div>
                        </div>
                                ";
                            }
                        ?>

                    </form>
                </div>
            </div>
        </div>
        <form action=""  method="post" id="deleteForm">
            <input type="hidden" id="delete_id" name="delete_id">
        </form>

        <div class="card-body">
            <table class="table table-striped" id="table1">
                <thead>
                <tr>
                    <th>Piatto</th>
                    <th>Descrizione</th>
                    <th>Categoria</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $productRetriever = "
                                        SELECT products.product_id, products.name, products.description, products.category, products.price
                                        FROM products, menus
                                        WHERE menu = ? 
                                        and products.menu = menus.menu_id
                                        AND menus.name = 'asporto'
                                        ORDER BY 
                                        CASE 
                                            WHEN category = 'Antipasti' THEN 1
                                            WHEN category = 'Primi piatti' THEN 2
                                            WHEN category = 'Secondi piatti' THEN 3
                                            WHEN category = 'Pizze' THEN 4
                                            WHEN category = 'Contorni' THEN 5
                                            WHEN category = 'Dolci' THEN 6
                                            WHEN category = 'Bevande' THEN 7
                                            ELSE 8  -- Qualsiasi altra categoria andrà in fondo
                                        END;
                                        ";

                $stmt = $mysqli->prepare($productRetriever);
                $stmt->bind_param("i", $stmtRow["menu_id"]);
                $stmt->execute();
                $stmtProduct = $stmt->get_result();

                if($stmtProduct->num_rows > 0) {
                while($rowProduct = $stmtProduct->fetch_assoc()) {
                echo "<tr>";
                    echo "<td>" . $rowProduct["name"] . "</td>";
                    echo "<td>" . $rowProduct["description"] . "</td>";
                    echo "<td>" . $rowProduct["category"] . "</td>";
                    echo "<td>" . $rowProduct["price"] . "€" ."</td>";
                    echo "<td> <a href=\"#\" id='toast-success' class=\"btn btn-outline-danger\" onclick='deletePlate(" . $rowProduct['product_id'] . ")'>Elimina</a> </td>";
                    echo "</tr>";
                }
                } else {
                echo "<tr>";
                    echo "<td colspan='5'>Non hai alcun prodotto nel tuo menu</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

            <footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>2025 &copy; BiteLine</p>
        </div>
    </div>
</footer>
        </div>
    </div>
    <script src="../../../../assets/bootstrap/static/js/components/dark.js"></script>
    <script src="../../../../assets/bootstrap/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <script src="../../../../assets/script/plateDeleteHandler.js"></script>
    <script src="../../../../assets/bootstrap/compiled/js/app.js"></script>


    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-image-filter/filepond-plugin-image-filter.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js"></script>
    <script src="../../../../assets/bootstrap/extensions/filepond/filepond.js"></script>

    <script src="../../../../assets/bootstrap/extensions/sweetalert2/sweetalert2.min.js"></script>>
    <script src="../../../../assets/script/successAlertSweetalert.js"></script>>

    <script src="../../../../assets/bootstrap/extensions/toastify-js/src/toastify.js"></script>

    <script src="../../../../assets/bootstrap/static/js/pages/filepond.js"></script>
<script>
    document.querySelector('.sidebar-toggle').addEventListener('click', () => {
        document.querySelector('.email-app-sidebar').classList.toggle('show')
    })
    document.querySelector('.sidebar-close-icon').addEventListener('click', () => {
        document.querySelector('.email-app-sidebar').classList.remove('show')
    })
    document.querySelector('.compose-btn').addEventListener('click', () => {
        document.querySelector('.compose-new-mail-sidebar').classList.add('show')
    })
    document.querySelector('.email-compose-new-close-btn').addEventListener('click', () => {
        document.querySelector('.compose-new-mail-sidebar').classList.remove('show')
    })
</script>

</body>

</html>