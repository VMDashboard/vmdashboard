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
      $ret = "Unable to save configuration, check folder permissions";
    }
  } //End else statement for database connection check
} // End if statement for POST data

?>


<!doctype html>
<html lang="en">
  <head>
    <title>OpenVM Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />

    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Dashboard CSS -->
    <link rel="stylesheet" href="../../assets/css/material-dashboard.css?v=2.0.2">

    <link rel="stylesheet" href="../../assets/css/openvm-dashboard.css">

  </head>
  <body class="off-canvas-sidebar">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white" id="navigation-example">
    	<div class="container">
        <div class="navbar-wrapper"></div>

    		<button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
          <span class="sr-only">Toggle navigation</span>
    			<span class="navbar-toggler-icon icon-bar"></span>
    			<span class="navbar-toggler-icon icon-bar"></span>
    			<span class="navbar-toggler-icon icon-bar"></span>
    		</button>

      </div>
    </nav>
    <!-- End Navbar -->


    <div class="wrapper wrapper-full-page">

      <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('../../assets/img/bg/dorin-vancea-83926-unsplash.jpg'); background-size: cover; background-position: top center;">
      <!-- Photo src free from Unsplash.com, THANK YOU! -->
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
        <div class="container">
          <div class="col-lg-4 col-md-6 col-sm-6 ml-auto mr-auto">
            <form class="form" method="post" action="">
              <div class="card card-login">
                <div class="card-header card-header-warning text-center">
                  <h4 class="card-title">Database Setup</h4>

                </div>
                <div class="card-body ">
                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">layers</i>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="Database Name..." name="db_name" required="" >
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">face</i>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="Database Username..." name="db_user" required="" >
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">lock_outline</i>
                        </span>
                      </div>
                      <input type="password" class="form-control" placeholder="Database Password" name="db_password" required="" >
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">storage</i>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="Database Host" name="db_host" required="" >
                    </div>
                  </span>

                  <span id="errorMessage" class="confirmMessage" style="text-align:center;"><p><?php echo $ret; ?></p></span>

                </div>
                <div class="card-footer justify-content-center">
                  <input type="submit" class="btn btn-warning" value="Get Started" >
                  <!-- <a href="#pablo" class="btn btn-rose btn-link btn-lg">Lets Go</a> -->
                </div>
              </div>
            </form>
          </div>
        </div>

        <footer class="footer" >
          <div class="container">
            <nav class="float-left">
              <ul>
                <li>
                  <a href="https://openvm.tech">
                    <img src="../../assets/img/squarelogo.png" width="20px"> &nbsp OPENVM.TECH
                  </a>
                </li>
                <li>
                  <a href="https://openvm.tech/about/">
                    About
                  </a>
                </li>
                <li>
                  <a href="https://openvm.tech/news/">
                    News
                  </a>
                </li>
                <li>
                  <a href="https://openvm.tech/feedback/">
                    Feedback
                  </a>
                </li>
              </ul>
            </nav>

            <div class="copyright float-right">
              &copy;
              <script>
                document.write(new Date().getFullYear())
              </script>, OpenVM.
            </div>
          </div>
        </footer>
      </div>
    </div>


    <!--   Core JS Files   -->
    <script src="../../assets/js/core/jquery.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap-material-design.min.js"></script>

    <script src="https://unpkg.com/default-passive-events"></script>

    <!--  Notifications Plugin, full documentation here: http://bootstrap-notify.remabledesigns.com/    -->
    <script src="../../assets/js/plugins/bootstrap-notify.js"></script>

    <!--  Charts Plugin, full documentation here: https://gionkunz.github.io/chartist-js/ -->
    <script src="../../assets/js/core/chartist.min.js"></script>

    <!-- Plugin for Scrollbar documentation here: https://github.com/utatti/perfect-scrollbar -->
    <script src="../../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>

    <!-- Material Dashboard Core initialisations of plugins and Bootstrap Material Design Library -->
    <script src="../../assets/js/material-dashboard.js?v=2.1.0"></script>

  </body>
</html>
