<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if ($_POST['password'] == $_POST['confirm_password']) {
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['action'] = $_POST['action'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
  } else {
    $_SESSION['reset_status'] = "Password was not changed!";
  }
}

// Time to bring in the header
require('../header.php');

if ($_SESSION['action'] == 'Change') {
  $username = $_SESSION['username'];
  $password = $_SESSION['password'];

  // Hash and salt password with bcrypt
  $hash = password_hash($password, PASSWORD_BCRYPT);

  require('config.php');

  // Adding the user
  $sql = "UPDATE openvm_users SET password='$hash' WHERE username='$username'";
    // Executing the SQL statement
    if ($conn->query($sql) === TRUE) {
      //Unset the SESSION variables
      unset($_SESSION['password']);
      unset($_SESSION['action']);
      $_SESSION['reset_status'] = "Password Change Successful!";
    }
  $conn->close();
}

if (isset($_SESSION['reset_status'])) {
  $ret = $_SESSION['reset_status'];
  echo "
    <script>
    var alert_msg = \"$ret\"
    swal(alert_msg);
    </script>";

  unset($_SESSION['reset_status']);
  if ($ret == "Password Change Successful!") {
    header('Location: ../../index.php');
  }
}

require('../navbar.php');

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


<div class="content">
  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">album</i>
            </div>
            <h3 class="card-title">Password Change</h3>
            <p class="card-category">User: <?php echo $_SESSION['username']; ?></p>
          </div>
          <div class="card-body">
            <br />

            <div class="row">
              <label class="col-5 col-form-label">New Password: </label>
              <div class="col-4">
                <div class="form-group">
                  <input type="password" required="required" placeholder="New Password" class="form-control" name="password" id="pass1" onfocusout="checkPassword();"/>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-5 col-form-label">New Password: </label>
              <div class="col-4">
                <div class="form-group">
                  <input type="password" required="required" placeholder="Confirm Password" class="form-control" name="confirm_password" id="pass2" onkeyup="checkPassword();"/>
                </div>
              </div>
            </div>

            <span id="confirmMessage" class="confirmMessage"></span>

          </div> <!-- end card body -->
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-warning">Submit</button>
          </div>
        </form>
      </div> <!-- end card -->
    </div>
  </div>
</div> <!-- end content -->

<?php
require('../footer.php');
?>
