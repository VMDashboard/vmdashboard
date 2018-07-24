<?php

// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

require('config.php');

if (isset($_SESSION['username']) || $_SESSION['initial_setup'] == true) {

  if (isset($_POST['account']) && $_POST['password'] == $_POST['confirm_password']){

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
          $ret = $conn->error;
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


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/squarelogo.png">
    <link rel="icon" type="image/png" href="../../assets/img/squarelogo.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      OpenVM
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
  //If there is a returned error, display it in an alert.
  if (isset($ret)){
    ?>
    <script src="../../assets/js/plugins/sweetalert2.min.js"></script>
    <script>
    var alert_msg = "<?php echo $ret; ?>";
    swal(alert_msg);
    </script>
    <?php
  }
  ?>

  <div class="wrapper wrapper-full-page ">
    <div class="full-page section-image" filter-color="black" data-image="../../assets/img/bg/leonard-cotte.jpg">
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
      <div class="content">
        <div class="container">
          <div class="col-lg-4 col-md-6 ml-auto mr-auto">
            <form class="form" method="post" action="">
              <div class="card card-login">
                <div class="card-header ">
                  <div class="card-header ">
                    <h3 class="header text-center">User Setup</h3>
                  </div>
                </div>
                <div class="card-body ">

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-single-02"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" placeholder="Username" name="username" required="" >
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-email-85"></i>
                      </span>
                    </div>
                    <input type="email" class="form-control" placeholder="Email Address" name="email" required="" >
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-key-25"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control" placeholder="Password" name="password" required="" id="pass1" onfocusout="checkPassword();">
                  </div>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-key-25"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required="" id="pass2" onkeyup="checkPassword();">
                  </div>

                  <span id="confirmMessage" class="confirmMessage"></span>

                </div>
                <div class="card-footer ">
                  <input type="submit" class="btn btn-warning btn-round btn-block mb-3" value="Get Started" name="account" >
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
                  <li><a href="https://openvm.tech">OpenVM.tech</a></li>
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
  <!-- Chart JS -->
  <script src="../../assets/js/plugins/chartjs.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>

  <script>
  function checkFullPageBackgroundImage() {
    $page = $('.full-page');
    image_src = $page.data('image');

    if (image_src !== undefined) {
      image_container = '<div class="full-page-background" style="background-image: url(' + image_src + ') "/>';
      $page.append(image_container);
    }
  }

  $(document).ready(function() {
    checkFullPageBackgroundImage();
  });
  </script>

</body>
</html>
