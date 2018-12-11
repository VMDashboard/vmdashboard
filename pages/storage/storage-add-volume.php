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
  $_SESSION['volume_image_name'] = clean_input($_POST['volume_image_name']);
  $_SESSION['volume_size'] = $_POST['volume_size'];
  $_SESSION['unit'] = $_POST['unit'];
  $_SESSION['driver_type'] = $_POST['driver_type'];

  header("Location: ".$_SERVER['PHP_SELF']."?pool=".$_SESSION['pool']);
  exit;
}

require('../header.php');

if (isset($_SESSION['pool'])) {

  $pool = $_SESSION['pool'];
  $volume_image_name = $_SESSION['volume_image_name'];
  $volume_capacity = $_SESSION['volume_size'];
  $volume_size = $_SESSION['volume_size'];
  $unit = $_SESSION['unit'];
  $driver_type = $_SESSION['driver_type'];

  //$notification = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "" : "Cannot add disk to the guest: ".$lv->get_last_error();
  $notification = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? "" : "Cannot create volume: ".$lv->get_last_error();

  unset($_SESSION['pool']);
  unset($_SESSION['volume_image_name']);
  unset($_SESSION['volume_size']);
  unset($_SESSION['unit']);
  unset($_SESSION['driver_type']);

  //Return back to the storage-pools page if successful
  if (!$notification){
    header('Location: storage-pools.php');
    exit;
  }
}

require('../navbar.php');

?>

<div class="content">
  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">device_hub</i>
            </div>
            <h3 class="card-title">Create Storage Volume</h3>
            <p class="card-category">Host: <?php echo $hn; ?></p>
          </div>
          <div class="card-body">
            <br />

            <div class="row">
              <label class="col-3 col-form-label">Volume Name: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="newVolume.qcow2" required="required" placeholder="Enter name for new volume image" class="form-control" name="volume_image_name" />
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Volume Size: </label>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control" required="required" value="40" min="1" name="volume_size">
                </div>
              </div>
              <div class="col-2 checkbox-radios">
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="unit" value="M"> MB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="unit" value="G" checked="checked"> GB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Driver Type: </label>
              <div class="col-6">
                <select  class="form-control" name="driver_type" onchange="newExtenstion(this.form)">
                  <option value="qcow2" selected>qcow2</option>
                  <option value="raw">raw</option>
                </select>
              </div>
            </div>

            <input type="hidden" value="<?php echo $_GET['pool']; ?>" name="pool"/>

          </div> <!-- end card body -->
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-warning">Submit</button>
          </div>
        </form>
      </div> <!-- end card -->
    </div>
  </div> <!-- end row -->
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

function newExtenstion(f) {
  var diskName = f.volume_image_name.value;
  diskName = diskName.replace(/\s+/g, '');
  var n = diskName.lastIndexOf(".");
  var noExt = n > -1 ? diskName.substr(0, n) : diskName;
  var driverType = f.driver_type.value;
  if (driverType === "qcow2"){
    var ext = ".qcow2";
    var fullDiskName = noExt.concat(ext);
    f.volume_image_name.value = fullDiskName;
  }
  if (driverType === "raw"){
    var ext = ".img";
    var fullDiskName = noExt.concat(ext);
    f.volume_image_name.value = fullDiskName;
  }
}
</script>

<?php
require('../footer.php');
?>
