<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}
// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: login.php');
}
// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['action'] = $_POST['action'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
// Time to bring in the header
require('header.php');


if ($_SESSION['action'] == 'Change') {
  $username = $_SESSION['username'];
  $password = $_SESSION['password'];

  // Hash and salt password with bcrypt
  $hash = password_hash($password, PASSWORD_BCRYPT);

  require('../config.php');

    // Adding the user
    $sql = "UPDATE openvm_users SET password='$hash' WHERE username='$username'";
      // Executing the SQL statement
      if ($conn->query($sql) === TRUE) {
        //Unset the SESSION variables
        unset($_SESSION['password']);
        unset($_SESSION['action']);
        $_SESSION['reset_status'] = true;
      }
      $conn->close();

}




require('navigation.php');
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

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>User</h3>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Reset Password:</h2>

          <div class="clearfix"></div>
        </div>

        <div class="x_content">

          <div class="col-md-9 col-sm-9 col-xs-12">

            <?php
            if ($_SESSION['reset_status'] == true) {
              echo "<h2>Password Reset Successful!</h2>";
              unset($_SESSION['reset_status']);
            }
            ?>

              <form action="" method="post">
                <div class="form-horizontal form-label-left" style="min-height: 250px;">

                  <div class="form-group">
                    <label for="pass1" class="control-label col-md-3 col-sm-3 col-xs-12">New Password</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <input type="password" name="password" class="form-control" placeholder="New Password" required="" id="pass1"/>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="pass2" class="control-label col-md-3 col-sm-3 col-xs-12">Confirm Password</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required="" id="pass2" onkeyup="checkPassword();" />
                    </div>
                  </div>
<div class="col-md-9 col-sm-9 col-xs-12">
                  <span id="confirmMessage" class="confirmMessage"></span>
</div>
                </div>

                <div class="actionBar">
                  <input type="submit" name="action" class="buttonFinish btn btn-default" value="Change" />
                </div>

              </form>



          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        <!-- /page content -->

<?php require('footer.php');?>
