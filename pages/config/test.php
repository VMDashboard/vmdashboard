<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['os'])) {
  $_SESSION['os'] = $_POST['os'];
}

require('../header.php');

if (isset($_SESSION['os'])) {

  $os = $_SESSION['os'];
  unset($_SESSION['os']);

  exec("wget -bqc http://releases.ubuntu.com/18.04/ubuntu-18.04-live-server-amd64.iso");

}

require('../navbar.php');

?>




<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Download ISO files</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <input type="radio" id="ubuntu" name="os" checked /> Ubuntu Server
            <input type="radio" id="centos" name="os" /> CentOS
          </div>
        </div>
      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-danger">Submit</button>
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
