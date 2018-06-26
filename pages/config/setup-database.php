<?php

// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If the database config.php file exists already redirect to index.php
$path = realpath(__DIR__) . "/config.php";
if (file_exists($path)) {
  header('Location: ../../index.php');
}

// Database names should be basic string characters without spaces or symbols
function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// Check for POST data, then create config.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Getting the POST information
  $db_name = clean_input($_POST['db_name']);
  $db_user = clean_input($_POST['db_user']);
  $db_password = $_POST['db_password'];
  $db_host = clean_input($_POST['db_host']);

  // Create the connection information for config.php
  $config_string = "<?php
  // Setting up the Database Connection
  \$db_host = '$db_host';
  \$db_user = '$db_user';
  \$db_password = '$db_password';
  \$db_name = '$db_name';
  \$conn = new mysqli(\$db_host, \$db_user, \$db_password, \$db_name);
  if (\$conn->connect_error) {
    die(\"Connection failed: \" . \$conn->connect_error);
  }
  ?>";

  // Attempt to make the database connection
  $conn = new mysqli($db_host, $db_user, $db_password, $db_name);
  // Check connection
  if ($conn->connect_error) {
      $ret = $conn->connect_error;
  } else {
    //Create config.php
    $config_file = "config.php";
    $config_create = file_put_contents($config_file, $config_string);

    //If config.php was created move to setup-user.php
    if($config_create){
      $_SESSION['initial_setup'] = true;
      header('Location: setup-user.php');
    } else {
      $config_create = "failed";
      $ret = "Configuration was not saved, check folder permissions";
    }

  } //End else statement for database connection check
} // End if statement for POST data

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/sqaurelogo.png">
    <link rel="icon" type="image/png" href="../../assets/img/squarelogo.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      openVM
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  </head>

<body class="login-page">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
    <div class="container">
      <div class="navbar-wrapper">
        <img src="../../assets/img/squarelogo.png" width="30px">
        <a class="navbar-brand" href="https://openvm.tech"> &nbsp OPENVM.TECH </a>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <?php
  if (isset($ret)){
    ?>
    <script src="../../assets/js/plugins/sweetalert2.min.js"></script>
    <script>
    var alert_msg = '<?php echo $ret; ?>';
    swal(alert_msg);
    </script>
    <?php
  }
  ?>

  <div class="wrapper wrapper-full-page ">
    <div class="full-page section-image" filter-color="black" data-image="../../assets/img/bg/fabio-mangione.jpg">
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
      <div class="content">
        <div class="container">
          <div class="col-lg-4 col-md-6 ml-auto mr-auto">
            <form class="form" method="post" action="">
              <div class="card card-login">
                <div class="card-header ">
                  <div class="card-header ">
                    <h3 class="header text-center">Database Setup</h3>
                  </div>
                </div>
                <div class="card-body">

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-single-copy-04"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" placeholder="Database Name" name="db_name" required="" >
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-single-02"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" placeholder="Database Username" name="db_user" required="" >
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-key-25"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control" placeholder="Database Password" name="db_password" required="" >
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-laptop"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" placeholder="Database Host" name="db_host" required="" >
                  </div>

                </div>
                <div class="card-footer ">
                  <input type="submit" class="btn btn-warning btn-round btn-block mb-3" value="Get Started" >
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
      <footer class="footer footer-black  footer-white ">
        <div class="container-fluid">
          <div class="row">
            <nav class="footer-nav">
              <ul>
                  <li><a href="https://openvm.tech">openVM.tech</a></li>
              </ul>
            </nav>
            <div class="credits ml-auto">
                <span class="copyright">
                    © <script>document.write(new Date().getFullYear())</script>, developed by Penning Development LLC
                </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../../assets/js/core/jquery.min.js"></script>
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="../../assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
  <script src="../../assets/js/plugins/bootstrap-switch.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="../../assets/js/plugins/sweetalert2.min.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="../../assets/js/plugins/jquery.validate.min.js"></script>
  <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="../../assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="../../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../../assets/js/plugins/bootstrap-datetimepicker.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
  <script src="../../assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="../../assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="../../assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="../../assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="../../assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Bootstrap Table -->
  <script src="../../assets/js/plugins/nouislider.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chart JS -->
  <script src="../../assets/js/plugins/chartjs.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>

  <script>
    $(document).ready(function() {
      demo.checkFullPageBackgroundImage();
    });
  </script>
</body>

</html>
