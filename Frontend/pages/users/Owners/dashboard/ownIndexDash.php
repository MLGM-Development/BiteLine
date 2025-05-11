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

$ownerId = $payload['id'];

$identity = 'SELECT * FROM owners WHERE owner_id = ?';
$stmtIdentity = $mysqli->prepare($identity);
$stmtIdentity->bind_param('i', $ownerId);
$stmtIdentity->execute();
$identityResult = $stmtIdentity->get_result();
$identityRow = $identityResult->fetch_assoc();

$sql = 'SELECT * FROM restaurants WHERE owner = ?';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $ownerId);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $empNumb = "SELECT COUNT(*) FROM employees_history WHERE restaurant_id = ? AND start_date IS NOT NULL";
    $stmtempNumb = $mysqli->prepare($empNumb);
    $stmtempNumb->bind_param("i", $row["restaurant_id"]);
    $stmtempNumb->execute();
    $resultEmp = $stmtempNumb->get_result();
    $rowEmp = $resultEmp->fetch_assoc();

    $numOrder = "SELECT COUNT(DISTINCT user_id) as Conto FROM orders WHERE restaurant_id = ?";
    $stmtNumOrder = $mysqli->prepare($numOrder);
    $stmtNumOrder->bind_param("i", $row["restaurant_id"]);
    $stmtNumOrder->execute();
    $resultNumOrder = $stmtNumOrder->get_result();
    $rowNumOrder = $resultNumOrder->fetch_assoc();

    $numClient = "SELECT COUNT(*) FROM orders WHERE restaurant_id = ?";
    $stmtNumClient = $mysqli->prepare($numClient);
    $stmtNumClient->bind_param("i", $row["restaurant_id"]);
    $stmtNumClient->execute();
    $resultNumClient = $stmtNumClient->get_result();
    $rowNumClient = $resultNumClient->fetch_assoc();

    $topOrders = "SELECT * FROM orders, users WHERE restaurant_id = ? AND orders.user_id = users.user_id ORDER BY order_date DESC LIMIT 3";
    $stmtTopOrders = $mysqli->prepare($topOrders);
    $stmtTopOrders->bind_param("i", $row["restaurant_id"]);
    $stmtTopOrders->execute();
    $resultTopOrders = $stmtTopOrders->get_result();

    $lastApply = "SELECT * FROM employees_history, users WHERE employee_id = user_id AND restaurant_id = ? AND start_date IS NULL LIMIT 3";
    $stmtLastApply = $mysqli->prepare($lastApply);
    $stmtLastApply->bind_param("i", $row["restaurant_id"]);
    $stmtLastApply->execute();
    $resultLastApply = $stmtLastApply->get_result();

    $graphDataRetriever = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS orders_count FROM orders GROUP BY month ORDER BY month";
    $stmtGraphData = $mysqli->prepare($graphDataRetriever);
    $stmtGraphData->execute();
    $resultGraphData = $stmtGraphData->get_result();
    $data=[];
    $labels=[];
    while ($row = $resultGraphData->fetch_assoc()) {
        $graphData[] = [
            $labels[] = $row['month'],
            $data[] = $row['orders_count']
        ];
    }

}else{
    header('Location: ../../../location/managing/RegRestaurant.php');
}


?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area amministrazione | BiteLine</title>

    <link rel="shortcut icon" href="../../../../assets/media/images/favicon/favicon.png" type="image/x-icon">

  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/app.css">
  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/app-dark.css">
  <link rel="stylesheet" href="../../../../assets/bootstrap/compiled/css/iconly.css">
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
                class="sidebar-item active ">
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
            
<div class="page-heading">
    <h3>Homepage</h3>
</div> 
<div class="page-content"> 
    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldShow"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Dipendenti</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?php echo $rowEmp["COUNT(*)"] ?>
                                    </h6>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldAdd-User"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Clienti</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?php echo $rowNumOrder["Conto"] ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldBookmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Ordini</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?php echo $rowNumClient["COUNT(*)"] ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Statistiche numero ordini</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Ultime Candidature</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Posizione desiderata</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    // Output di ogni riga
                                    while($row = $resultLastApply->fetch_assoc()) {
                                    $nome_completo = htmlspecialchars($row["name"] . " " . $row["l_name"]);
                                    $posizione = htmlspecialchars($row["role"]);
                                    ?>
                                    <tr>
                                        <td class="col-3">
                                            <div class="d-flex align-items-center">
                                                <p class="font-bold ms-3 mb-0"><?php echo $nome_completo; ?></p>
                                            </div>
                                        </td>
                                        <td class="col-auto">
                                            <p class="mb-0"><?php echo $posizione; ?></p>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="ms-3 name">
                            <h5 class="font-bold">
                                <?php echo $identityRow["name"] . " " . $identityRow["l_name"]?>
                            </h5>
                            <h6 class="text-muted mb-0" style="text-transform: lowercase">
                                <?php echo "@" . $identityRow["name"] . $identityRow["l_name"]?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Ultimi ordini</h4>
                </div>
                <div class="card-content pb-4">

                    <?php
                    // Output di ogni riga
                    while($row = $resultTopOrders->fetch_assoc()) {


                        ?>
                        <div class="recent-message d-flex px-4 py-3">

                            <div class="name ms-4">
                                <h5 class="mb-1"><?php echo htmlspecialchars($row["name"] . " " . $row["l_name"]); ?></h5>
                                <h6 class="text-muted mb-0">€<?php echo htmlspecialchars($row["subtotal"]); ?> <?php echo htmlspecialchars($row["status"]); ?></h6>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="px-4">
                        <a href="table-datatable.php" class="btn btn-block btn-xl btn-outline-primary font-bold mt-3">Vai agli ordini</a>
                    </div>
                </div>
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
    


<script src="../../../../assets/bootstrap/extensions/apexcharts/apexcharts.min.js"></script>

    <script>
        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Ordini',
                data: <?= json_encode($data) ?>
            }],
            xaxis: {
                categories: <?= json_encode($labels) ?>,
                title: {
                    text: 'Mese'
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart-profile-visit"), options);
        chart.render();
    </script>

</body>

</html>