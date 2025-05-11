<?php
$mysqli = require __DIR__ . "/../../../../../Backend/Database/connection.php";
require __DIR__ . '/../../../../../Backend/Controllers/Cookies/JWT_Verifier.php';



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

$restIdRetriever = "SELECT restaurant_id FROM restaurants WHERE owner = ?";
$stmt = $mysqli->prepare($restIdRetriever);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$restaurantId = $row['restaurant_id'];

$sql = "SELECT * FROM employees_history, users WHERE restaurant_id = ? AND employees_history.employee_id = users.user_id AND employees_history.start_date IS NOT NULL AND employees_history.end_date IS NULL";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $restaurantId);
$stmt->execute();
$result = $stmt->get_result();




?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordini | BiteLine</title>



    <link rel="shortcut icon" href="../../../../assets/media/images/favicon/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../../../../assets/bootstrap/extensions/simple-datatables/style.css">


    <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/table-datatable.css">
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
                        class="sidebar-item">
                        <a href="table-datatable.php" class='sidebar-link'>
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Ordini</span>
                        </a>


                    </li>

                    <li
                        class="sidebar-item  has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-collection-fill"></i>
                            <span>Crea Menu</span>
                        </a>

                        <ul class="submenu ">

                            <li class="submenu-item  ">
                                <a href="table-menu-creator.php" class="submenu-link">Menu Tavolo</a>

                            </li>

                            <li class="submenu-item  ">
                                <a href="deliver-menu-creator.php" class="submenu-link">Asporto/Consegna</a>

                            </li>
                        </ul>


                    </li>

                    <li
                        class="sidebar-item ">
                        <a href="application-sent.php" class='sidebar-link'>
                            <i class="bi bi-person-plus-fill"></i>
                            <span>Candidature</span>
                        </a>


                    </li>

                    <li
                        class="sidebar-item active ">
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

        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Candidature</h3>
                        <p class="text-subtitle text-muted">Qua potrai visualizzare tutte le candidature inviate per il tuo ristorante</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="ownIndexDash.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Candidature</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Tutti le candidature
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="table1">
                            <thead>
                            <tr>
                                <th>Candidato</th>
                                <th>Ruolo</th>
                                <th>Curriculum</th>
                                <th>Stato</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["name"] . " " . $row["l_name"] ."</td>";
                                    echo "<td>" . $row["role"] . "</td>";
                                    echo "<td><a href='../../../../assets/documents/applications/" . $row["cv_path"] . "' class='btn btn-primary btn-sm' download><i class='bi bi-download'></i> Scarica CV</a></td>";
                                    echo "
                                            <td>
                                                <form action=\"../../../../../Backend/dbInserter/fire.php\" method=\"POST\">
                                                    <input type=\"hidden\" name=\"fire\" value=\"fire\">
                                                    <input type=\"hidden\" name=\"person\" value=\"" . $row["employee_id"] . "\">
                                                    <input type=\"submit\" value=\"Licenzia\" class=\"btn btn-danger btn-sm\">
                                                </form>
                                            </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='5'>Non hai alcun dipendente</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
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
<script src="../../../../assets/bootstrap/compiled/js/app.js"></script>
<script src="../../../../assets/bootstrap/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="../../../../assets/bootstrap/static/js/pages/simple-datatables.js"></script>

</body>
</html>