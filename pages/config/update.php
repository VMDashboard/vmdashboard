<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  $_SESSION['return_location'] = $_SERVER['PHP_SELF']; //sets the return location used on login page
  header('Location: ../login.php');
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['update'])) {
    $_SESSION['update'] = $_POST['update'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Add the header information
require('../header.php');

//Set variables

// Domain Actions
if (isset($_SESSION['update'])) {
  $path = exec("which git"); //determine the absolute path to git
  ($path == "") ? $ret = "It does not appear as though git is installed" : $ret = "";
  $tmp = shell_exec("cd .. && cd .. && $path pull 2>&1"); //run git at the web root directory. Use shell_exec to display all the output, not just last line. Redirect STDERR and STDOUT to variable
  //Remove session variables so that if page reloads it will not perform actions again
  unset($_SESSION['update']);
}

require('../navbar.php');

//Will display a sweet alert if a return message exists
if ($ret != "") {
echo "
<script>
var alert_msg = '$ret'
swal(alert_msg);
</script>";
}

//End PHP Section
?>

<div class="content">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"> Update </h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">

          <?php

          $arrayLatest = file('https://raw.githubusercontent.com/PenningDevelopment/openVM/master/pages/config/version.php');
          $arrayCurrent = file('../config/version.php');
          ($arrayLatest[1] > $arrayCurrent[1]) ? $update_available = true : $update_available = false;

          if ($update_available == true) { ?>
            <h6>There is an update available!</h6>
            <p>The current version is <?php echo $arrayLatest[1]; ?> </p>
            <p>Your version is <?php echo $arrayCurrent[1]; ?></p>
            <form action="" method="post">
              <input type="submit" name="update" value="Update Now">
            </form>
          <?php }

          if ($update_available == false) { ?>
            <h6>You are running the lastest version of OPENVM Dashboard.</h6>
            <p>Your version is <?php echo $arrayCurrent[1]; ?></p>
          <?php } ?>

          <br /><pre><?php echo $tmp; ?></pre><br />

          <?php
          $changeLog = file('../../CHANGELOG.md');
          $length = count($changeLog);
          for ($i = 1; $i < $length - 1; $i++) {
            echo "$changeLog[$i]";
          }
          ?>


        </div>
      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
