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
if (isset($_GET['action'])) {
    $_SESSION['action'] = $_GET['action'];
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Add the header information
require('../header.php');

//Set variables

// Domain Actions
if ($action == 'update') {
  $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
}

require('../navbar.php');



//Remove session variables so that if page reloads it will not perform actions again
unset($_SESSION['action']);


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
      <h4 class="card-title"> Update ?> </h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">


          <?php

          $arrayLatest = file('https://raw.githubusercontent.com/PenningDevelopment/openVM/master/pages/config/version.php');
          $arrayCurrent = file('../config/version.php');
          if ($arrayLatest[1] > $arrayCurrent[1])
            $update_available = true;

          echo "You are running OPENVM version $arrayCurrent[1]."

          if ($update_available == true) { ?>
            There is an update available. The current version is <?php echo $arrayLatest[1]; ?>.
            <input type="submit" name="update" value="Update Now">
          <?php } ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
