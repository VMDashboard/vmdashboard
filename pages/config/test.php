<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

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

require('../header.php');
require('../navbar.php');

$arrayVersion = file('https://raw.githubusercontent.com/PenningDevelopment/openVM/master/pages/config/version.php');

?>

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Test Page</h4>
      </div>
      <div class="card-body">
        <?php
        var_dump($arrayVersion);
        echo "<br>";
        echo "<br>";
        echo $arrayVersion[0];
        //echo "<br>";
        //echo "<br>";
        //if ($arrayVersion[0] > 0.0.0)
        //  echo "Version $arrayVersion[0] has been released";
        ?>
      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <input type="submit" class="btn btn-danger" name="action" value="Change" >
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
