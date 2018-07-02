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
  $_SESSION['ppol'] = $_POST['pool'];
}

require('../header.php');

if (isset($_SESSION['os'])) {
  $os = $_SESSION['os'];
  $pool = $_SESSION['pool'];
  unset($_SESSION['os']);
  unset($_SESSION['pool']);
  exec("wget -bqc http://releases.ubuntu.com/18.04/ubuntu-18.04-live-server-amd64.iso && mv ubuntu-18.04-live-server-amd64.iso $pool");
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
            <input type="radio" id="ubuntu" value="ubuntu" name="os" checked /> Ubuntu Server <br />
            <input type="radio" id="centos" value="centos" name="os" /> CentOS <br />
            <br />
            <select  class="form-control" name="pool">

            <?php
            $pools = $lv->get_storagepools();

            if (empty($pools)) {
              echo "<option value=\"none\">No Pool Available</option>";
            } else {
              for ($i = 0; $i < sizeof($pools); $i++) {
                //get the pool resource to use with refreshing the pool data
                $res = $lv->get_storagepool_res($pools[$i]);
                //refreshing the data before displaying because ISOs were not refreshing automatically and also the Available data was not correct after adding volumes
                $msg = $lv->storagepool_refresh($res) ? "Pool has been refreshed" : "Error refreshing pool: ".$lv->get_last_error();
                //getting the pool information to display the data in a table
                $info = $lv->get_storagepool_info($pools[$i]);

                $poolName = $pools[$i];
                $poolPath = $info['path'];

                $act = $info['active'] ? 'Active' : 'Inactive';
                if ($act == "Active")
                  echo "<option value=$poolPath\">$poolName</option>";
              } //ends the for loop for each storage pool
            } ?>

          </select>



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
