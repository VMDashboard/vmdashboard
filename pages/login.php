<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}
//Grab post infomation and add new drive
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  require('config/config.php');

  //Use apps/password_compat for PHP version 5.4. Needed for CentOS 7 default version of PHP
  if (version_compare(PHP_VERSION, '5.5.0', '<')) {
  require('../apps/password_compat/lib/password.php');
  }

  $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
  $password = $_POST['password'];

  // Creating the SQL statement
  $sql = "SELECT password, userid FROM vmdashboard_users WHERE username = '$username' LIMIT 1;";

  // Executing the SQL statement
  $result = $conn->query($sql);

  // Extracting the record and storing the hash
  while ($row = $result->fetch_assoc()) {
	   $hash = $row['password'];
     $userid = $row['userid'];
  }

  //Verifying the password to the hash in the database
  if (password_verify($password, $hash)) {
    //Set the username session to keep logged in
    $_SESSION['username'] = $username;
    $_SESSION['userid'] = $userid; //used to set items such as themeColor in index.php

    $arrayLatest = file('https://vmdashboard.org/version.php'); //Check for a newer version of OpenVM
    $arrayExisting = file('config/version.php'); //Check the existing version of OpenVM
    $latestExploded = explode('.', $arrayLatest[1]); //Seperate Major.Minor.Patch
    $existingExploded = explode('.', $arrayExisting[1]); //Seperate Major.Minor.Patch
    $latest = $latestExploded[0] . $latestExploded[1] . $latestExploded [2];
    $existing = $existingExploded[0] . $existingExploded[1] . $existingExploded[2];

    //Compare each component Major, Minor, and Patch
    if ($latest > $existing) {
      $_SESSION['update_available'] = true;
      $_SESSION['update_version'] = $arrayLatest;
    }

    //Setting the user's theme color choice
    $sql = "SELECT value, userid FROM vmdashboard_config WHERE name = 'theme_color';";
    $result = $conn->query($sql);
    // Extracting the record
    if (mysqli_num_rows($result) != 0 ) {
      while ($row = $result->fetch_assoc()) {
        if ($_SESSION['userid'] == $row['userid']){
          $_SESSION['themeColor'] = $row['value'];
        }
      }
    } else {
      $_SESSION['themeColor'] = "white";
    }

    //Setting the user's language choice
    $sql = "SELECT value, userid FROM vmdashboard_config WHERE name = 'language';";
    $result = $conn->query($sql);
    // Extracting the record
    if (mysqli_num_rows($result) != 0 ) {
      while ($row = $result->fetch_assoc()) {
        if ($_SESSION['userid'] == $row['userid']){
          $_SESSION['language'] = $row['value'];
        }
      }
    } else {
      $_SESSION['language'] = "english";
    }

    //Send the user back to the page they came from or to index.php
    if(isset($_SESSION['return_location'])) {
      $return_url = $_SESSION['return_location'];
      unset($_SESSION['return_location']);
      header('Location: '.$return_url);
    } else {
      header('Location: ../index.php');
    }
  } else {
    //If credentials were not a correct match
    $ret = "Credentials are incorrect";
  }

  $conn->close();

}
?>




<!doctype html>
<html lang="en">
  <head>
    <title>VM Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />

    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/material-dashboard.css?v=2.0.2">

    <link rel="stylesheet" href="../assets/css/vm-dashboard.css">

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

      <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('../assets/img/bg/vidar-nordli-mathisen-544510-unsplash.jpg'); background-size: cover; background-position: top center;">
      <!-- Photo src free from Unsplash.com, THANK YOU! -->
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
        <div class="container">
          <div class="col-lg-4 col-md-6 col-sm-6 ml-auto mr-auto">
            <form class="form" method="post" action="">
              <div class="card card-login">
                <div class="card-header card-header-warning text-center">
                  <h4 class="card-title">Login</h4>

                </div>
                <div class="card-body ">
                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">face</i>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="User Name..." name="username">
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">lock_outline</i>
                        </span>
                      </div>
                      <input type="password" placeholder="Password" class="form-control" name="password">
                    </div>
                  </span>


                  <span id="confirmMessage" class="confirmMessage" style="text-align:center;"></span> <br />
                  <span id="errorMessage" class="confirmMessage" style="text-align:center;"><p><?php echo $ret; ?></p></span>


                </div>
                <div class="card-footer justify-content-center">
                  <input type="submit" class="btn btn-warning" value="Get Started" name="account">
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
                  <a href="https://vmdashboard.org">
                    <img src="../assets/img/squarelogo.png" width="20px"> &nbsp VMDASHBOARD.ORG
                  </a>
                </li>
                <li>
                  <a href="https://vmdashboard.org/about/">
                    About
                  </a>
                </li>
                <li>
                  <a href="https://vmdashboard.org/news/">
                    News
                  </a>
                </li>
                <li>
                  <a href="https://vmdashboard.org/feedback/">
                    Feedback
                  </a>
                </li>
              </ul>
            </nav>

            <div class="copyright float-right">
              &copy;
              <script>
                document.write(new Date().getFullYear())
              </script>, VM Dashboard.
            </div>
          </div>
        </footer>
      </div>
    </div>


    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap-material-design.min.js"></script>

    <script src="https://unpkg.com/default-passive-events"></script>

    <!--  Notifications Plugin, full documentation here: http://bootstrap-notify.remabledesigns.com/    -->
    <script src="../assets/js/plugins/bootstrap-notify.js"></script>

    <!--  Charts Plugin, full documentation here: https://gionkunz.github.io/chartist-js/ -->
    <script src="../assets/js/core/chartist.min.js"></script>

    <!-- Plugin for Scrollbar documentation here: https://github.com/utatti/perfect-scrollbar -->
    <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>

    <!-- Material Dashboard Core initialisations of plugins and Bootstrap Material Design Library -->
    <script src="../assets/js/material-dashboard.js?v=2.1.0"></script>

  </body>
</html>
