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
    $_SESSION['reset_status'] = "<h2>Password was not changed!</h2><br><br>";
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
        $_SESSION['reset_status'] = "<h2>Password Change Successful!</h2><br><br>";
      }
      $conn->close();

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
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Change password for  <?php echo $_SESSION['username']; ?></h4>

        <?php
        if (isset($_SESSION['reset_status'])) {
          echo $_SESSION['reset_status'];
          unset($_SESSION['reset_status']);
        }
        ?>

      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#passwordChange" role="tab" data-toggle="tab">Change Password</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="passwordChange">

                <div class="row">
                  <label class="col-sm-2 col-form-label">New Password: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="password" required="required" placeholder="New Password" class="form-control" name="password" id="pass1"/>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">New Password: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="password" required="required" placeholder="Confirm Password" class="form-control" name="confirm_password" id="pass2" onkeyup="checkPassword();"/>
                    </div>
                  </div>
                </div>

                <span id="confirmMessage" class="confirmMessage"></span>

              </div> <!-- end tab pane -->
            </div> <!-- end tab content -->
          </div>

        </div>
      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <input type="submit" class="btn btn-danger" value="Change" >
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
