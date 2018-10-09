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

// Domain Actions
if (isset($_SESSION['update'])) {
  $path = exec("which git"); //determine the absolute path to git
  ($path == "") ? $notification = "It does not appear as though git is installed" : $notification = "";
  //If git is not installed, then do not run the git commands
  if ($path != "") {
    //$tmp = shell_exec("cd .. && cd .. && $path pull 2>&1"); //run git at the web root directory. Use shell_exec to display all the output, not just last line. Redirect STDERR and STDOUT to variable
    $setOrigin = shell_exec("cd .. && cd .. && $path remote set-url origin https://github.com/VMDashboard/vmdashboard.git 2>&1");
    $fetchOrigin = shell_exec("cd .. && cd .. && $path fetch origin master 2>&1");
    $resetOrigin = shell_exec("cd .. && cd .. && $path reset --hard origin/master 2>&1");
  }
}

$arrayLatest = $_SESSION['update_version'];
$arrayExisting = file('version.php');
$existingExploded = explode('.', $arrayExisting[1]);
$latestExploded = explode('.',$arrayLatest[1]);

if ($existingExploded >= $latestExploded) {
  //Remove session variables so that if page reloads it will not perform actions again
  unset($_SESSION['update']);
  unset($_SESSION['update_available']);
}

require('../navbar.php');

?>

<div class="content">
  <div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-left">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">album</i>
            </div>
            <h3 class="card-title">Update</h3>
            <p class="card-category">Installed version: <?php echo $arrayExisting[1]; ?></p>
          </div>
          <div class="card-body">
            <br />

            <?php
            //$_SESSION['update'] and $_SESSION['update_version'] are set in login.php

            if ($_SESSION['update_available'] == true) { ?>
              <h6>There is an update available!</h6>
              <p>The newest release is <?php echo $arrayLatest[1]; ?> </p>
              <input type="submit" name="update" value="Update Now" class="btn btn-warning">
            <?php }

            if ($_SESSION['update_available'] == false) { ?>
              <h6>You are running the lastest version of VM Dashboard.</h6>
            <?php } ?>

            <br />
            <pre><?php echo $fetchOrigin; ?></pre>
            <pre><?php echo $resetOrigin; ?></pre>
            <br />

            <?php
            //Display the changelog on the update page
            $changelog = file('../../changelog.php');
            $length = count($changelog);
            for ($i = 1; $i < $length; $i++) { //starting at index 1, 0 index is a php line.
              print $changelog[$i] . "<br />";
            }
            ?>

          </div> <!-- end card body -->
        </form>
      </div> <!-- end card -->
    </div>
  </div>
</div> <!-- end content -->

<script>
window.onload =  function() {
  <?php
  if ($notification) {
    echo "showNotification(\"top\",\"right\",\"$notification\");";
  }
  ?>
}

function showNotification(from, align, text){
    color = 'warning';
    $.notify({
        icon: "",
        message: text
      },{
          type: color,
          timer: 500,
          placement: {
              from: from,
              align: align
          }
      });
}
</script>

<?php
require('../footer.php');
?>
