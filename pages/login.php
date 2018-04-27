
<?php
//Grab post infomation and add new drive
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  require('../config.php');
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Creating the SQL statement
  $sql = "SELECT password FROM openvm_users WHERE username = '$username' LIMIT 1";

  // Executing the SQL statement
  $result = $conn->query($sql);

  // Extracting the record and storing the hash
  while ($row = $result->fetch_assoc()) {
	   $hash = $row['password'];
  }

  //Verifying the password to the hash in the database
  if (password_verify($password, $hash)) {
    session_start();
    $_SESSION['username'] = $username;
    header('Location: ../index.php');
   } else {
     echo "Credentials are incorrect";
   }

   $conn->close();
 }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="../assets/img/logo3.png" type="image/png" />

    <title>openVM</title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form method="post" action="login.php">
              <h1>Login Form</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                  <input style="float:none;margin:0px;" type="submit" name="login" value="Login" class="btn btn-default submit">
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="#" class="to_register"> View tutorials </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <span style="font-size:35px;">open(<font style="color:#FF8C00;">VM</font>)</span>
                  <p>©2018 All Rights Reserved.</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
  </body>
</html>
