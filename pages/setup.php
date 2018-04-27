<?php
//check for post next, create config.php
if (isset($_POST['database'])){
  $db_name = $_POST['db_name'];
  $db_user = $_POST['db_user'];
  $db_password = $_POST['db_password'];
  $db_host = $_POST['db_host'];
  $db_prefix = $_POST['db_prefix'];

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
  $config_file = "../config.php";
  $config_create = file_put_contents($config_file, $config_string);
  if($config_create)
    header('Location: setup.php#signup');
}
//check for post submit, use config.php to add user to database
if (isset($_POST['account'])){
  require('../config.php');

  //Capturing the POST Data
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  // Checking for Duplicate usernames
  $sql = "SELECT username FROM openvm_users WHERE username = '$username';";
  $result = $conn->query($sql);
  if (mysqli_num_rows($result) != 0 ) {
	   die("Username already exists");
   }
   // Checking for Duplicate emails
   $sql = "SELECT email FROM openvm_users WHERE email = '$email';";
   $result = $conn->query($sql);
   if (mysqli_num_rows($result) != 0 ) {
	    die("Email already exists");
    }
    // Hash and salt password with bcrypt
    $hash = password_hash($password, PASSWORD_BCRYPT);
    // Creating the SQL statement

    //Creating the users tables
    $sql = "CREATE TABLE openvm_users (
      username varchar(255),
      email varchar(255),
      password varchar(255))";
    $conn->query($sql);

    $sql = "INSERT INTO openvm_users (username, email, password)
      VALUES ('$username', '$email', '$hash');";
    // Executing the SQL statement
    if ($conn->query($sql) === TRUE) {
      header('Location: ../index.php');
    } else {
      echo "Error: " . $sql . " " . $conn->error;
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
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form method="post" action="setup.php">
              <h1>Connect to Database</h1>
              <div>
                <input type="text" name="db_name" class="form-control" placeholder="Database Name" required="" />
              </div>
              <div>
                <input type="text" name="db_user" class="form-control" placeholder="Database Username" required="" />
              </div>
              <div>
                <input type="password" name="db_password" class="form-control" placeholder="Database Password" required="" />
              </div>
              <div>
                <input type="text" name="db_host" class="form-control" placeholder="Database Host" required="" />
              </div>
              <div>
                <input style="float:none;margin:0px;" type="submit" name="database" value="Next" class="btn btn-default submit">
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

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form method="post" action="">
              <h1>Create Account</h1>
              <div>
                <input type="text" name="username" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="email" name="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" name="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <input style="float:none;margin:0px;" type="submit" name="account" value="Finish" class="btn btn-default submit">
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
    </div>
  </body>
</html>
