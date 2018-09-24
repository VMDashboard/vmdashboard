<?php

// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

require('config.php');

if (isset($_SESSION['username']) || $_SESSION['initial_setup'] == true) {

  if (isset($_POST['account']) && $_POST['password'] == $_POST['confirm_password']){

    //Use apps/password_compat for PHP version 5.4. Needed for CentOS 7 default version of PHP
    //if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    //require('../../apps/password_compat/lib/password.php');
    //}

    //Capturing the POST Data
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; //do not need to sanitize because it will be hashed

    // Checking for Duplicate usernames
    $sql = "SELECT username FROM openvm_users WHERE username = '$username';";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) != 0 ) {
	    $ret = "Username is not available";
    } else {
      // Checking for Duplicate emails
      $sql = "SELECT email FROM openvm_users WHERE email = '$email';";
      $result = $conn->query($sql);
      if (mysqli_num_rows($result) != 0 ) {
	      $ret = "Email address is already in use";
      } else {
        // Hash and salt password with bcrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

        //Creating the users tables
        $sql = "CREATE TABLE IF NOT EXISTS openvm_users (
          userid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          username varchar(255),
          email varchar(255),
          password varchar(255))";

        $conn->query($sql);

        // Adding the user
        $sql = "INSERT INTO openvm_users (username, email, password)
          VALUES ('$username', '$email', '$hash');";

        // Executing the SQL statement
        if ($conn->query($sql) === TRUE) {
          header('Location: ../../index.php');
        } else {
          $ret = "Error: " . $conn->error;
        }

        $conn->close();

      } //End else statement for email check
    } //End else statement for username check
  } //End if statement for POST data check
} //End if statement for SESSION data check

if (isset($_POST['password']) && $_POST['password'] != $_POST['confirm_password']){
 $ret = "Passwords did not match";
}
?>

<script>
function checkPassword()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match.
        //Set the color to the good color and inform
        //the user that they have entered the correct password
        pass2.style.backgroundColor = "#ffffff";
        message.style.color = goodColor;
        message.innerHTML = "<p>Passwords Match!</p>"
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "<p>Passwords Do Not Match!</p>"
    }
}
</script>


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

      <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('../../assets/img/bg/julien-millet-530509-unsplash.jpg'); background-size: cover; background-position: top center;">
      <!-- Photo src free from Unsplash.com, THANK YOU! -->
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
        <div class="container">
          <div class="col-lg-4 col-md-6 col-sm-6 ml-auto mr-auto">
            <form class="form" method="post" action="">
              <div class="card card-login">
                <div class="card-header card-header-warning text-center">
                  <h4 class="card-title">User Setup</h4>

                </div>
                <div class="card-body ">
                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">face</i>
                        </span>
                      </div>
                      <input type="text" class="form-control" placeholder="Username" name="username" required="" >
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">email</i>
                        </span>
                      </div>
                      <input type="email" class="form-control" placeholder="Email Address" name="email" required="" >
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">lock_outline</i>
                        </span>
                      </div>
                      <input type="password" class="form-control" placeholder="Password" name="password" required="" id="pass1" onfocusout="checkPassword();">
                    </div>
                  </span>

                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">lock</i>
                        </span>
                      </div>
                      <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required="" id="pass2" onkeyup="checkPassword();">
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
