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


require('../header.php');
require('../navbar.php');


?>

<div class="content">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"> Test</h4>
    </div>
    <div class="card-body">

        <div class="progress">
          <div id="prog2" class="progress-bar progress-bar-danger" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
