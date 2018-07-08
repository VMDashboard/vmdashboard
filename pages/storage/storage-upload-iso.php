<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['pool'])) {
  $_SESSION['pool'] = $_POST['pool'];
  //$_SESSION['volume_image_name'] = clean_input($_POST['volume_image_name']);
  //$_SESSION['volume_size'] = $_POST['volume_size'];
  //$_SESSION['unit'] = $_POST['unit'];
  //$_SESSION['driver_type'] = $_POST['driver_type'];

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if (isset($_SESSION['pool'])) {

  $pool = $_SESSION['pool'];
  //$volume_image_name = $_SESSION['volume_image_name'];
  //$volume_capacity = $_SESSION['volume_size'];
  //$volume_size = $_SESSION['volume_size'];
  //$unit = $_SESSION['unit'];
  //$driver_type = $_SESSION['driver_type'];

  $ret = shell_exec("virsh -c qemu:///system list --all 2>&1");


//$ret = $lv->storagevolume_upload($pool,"/var/www/html/openvm/pages/storage/ubuntu.iso",0,500) ? "ISO has been uploaded successfully" : "Cannot upload iso: ".$lv->get_last_error();

  //$ret = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  //$msg = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';

  unset($_SESSION['pool']);
  //unset($_SESSION['volume_image_name']);
  //unset($_SESSION['volume_size']);
  //unset($_SESSION['unit']);
  //unset($_SESSION['driver_type']);


  //header('Location: storage-pools.php');
  //exit;
}

require('../navbar.php');

//Will display a sweet alert if a return message exists
if ($ret != "") {
echo "
<script>
var alert_msg = \"$ret\"
swal(alert_msg);
</script>";
}

?>


<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Upload ISO image</h4>
        <?php echo $ret; ?>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#storageVolume" role="tab" data-toggle="tab">Storage Volume</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="storageVolume">

                <div class="row">
                  <label class="col-sm-2 col-form-label">Volume Name: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" value="newVolume.qcow2" required="required" placeholder="Enter name for new volume image" class="form-control" name="volume_image_name" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Volume Size: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="number" class="form-control" name="volume_size" min="1" value="40" required="required" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Unit Size: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select  class="form-control" name="unit">
                        <option value="M">MB</option>
                        <option value="G" selected>GB</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Driver Type: </label>
                  <div class="col-sm-7">
                    <select  class="form-control" name="driver_type" onchange="newExtenstion(this.form)">
                      <option value="qcow2" selected>qcow2</option>
                      <option value="raw">raw</option>
                    </select>
                  </div>
                </div>

                <input type="hidden" value="<?php echo $_GET['pool']; ?>" name="pool"/>

              </div> <!-- end tab pane -->
            </div> <!-- end tab content -->
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
