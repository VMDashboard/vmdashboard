<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
//if (!isset($_SESSION['username'])){
//  header('Location: login.php');
//}

require('../header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);


// This function is used to prevent any problems with user form input
function clean_input($data) {
  $data = trim($data); //remove spaces at the beginning and end of string
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data); //remove any spaces within the string
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $_SESSION['source_file'] = $_POST['source_file']; //determines if none, new, or existing disk is added
  $_SESSION['new_volume_name'] = clean_input($_POST['new_volume_name']); //the new name of the volume disk image
  $_SESSION['new_volume_size'] = $_POST['new_volume_size']; //number used for volume size
  $_SESSION['new_unit'] = $_POST['new_unit']; //determines MiB or GiB
  $_SESSION['new_volume_size'] = $_POST['new_volume_size'];
  $_SESSION['new_driver_type'] = $_POST['new_driver_type']; //qcow2 or raw
  $_SESSION['new_target_bus'] = $_POST['new_target_bus']; //virtio, ide, sata, or scsi - used when adding disk to domain
  $_SESSION['existing_driver_type'] = $_POST['existing_driver_type']; //qcow2 or raw
  $_SESSION['existing_target_bus'] = $_POST['existing_target_bus']; //virtio, ide, sata, or scsi

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}


if (isset($_SESSION['source_file'])) {
  $disk_type = "file";
  $disk_device = "disk";
  $driver_name = "qemu"; //not used
  $source_file = $_SESSION['source_file']; //determines if none, new, or existing disk is added

  if ($source_file == "new") {
    $pool = "default"; //using the default storage pool to store images
    $volume_image_name = clean_input($_SESSION['new_volume_name']); //the new name of the volume disk image
    $volume_capacity = $_SESSION['new_volume_size']; //number used for volume size
    $unit = $_SESSION['new_unit']; //determines MiB or GiB
    $volume_size = $_SESSION['new_volume_size'];
    $driver_type = $_SESSION['new_driver_type']; //qcow2 or raw
    //Create the new disk now
    $new_disk = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type);
    $target_bus = $_SESSION['new_target_bus']; //virtio, ide, sata, or scsi - used when adding disk to domain

  } elseif ($source_file == "none") {
    //
  } else {
    $driver_type = $_SESSION['existing_driver_type']; //qcow2 or raw
    $target_bus = $_SESSION['existing_target_bus']; //virtio, ide, sata, or scsi
  }

  $target_dev = ""; //changed to an autoincremting option below.

  //If $target_bus type is virtio then we need to determine highest assigned value of drive, ex. vda, vdb, vdc...
  if ($target_bus == "virtio"){
    $virtio_array = array();
    for ($i = 'a'; $i < 'z'; $i++)
      $virtio_array[] = "vd" . $i;

    $tmp = libvirt_domain_get_disk_devices($dom);
    $result = array_intersect($virtio_array,$tmp);
    if (count($result) > 0) {
      $highestresult = max($result);
      $target_dev = ++$highestresult;
    } else {
      $target_dev = "vda";
    }
  }

  //If $target_bus type is ide then we need to determine highest assigned value of drive, ex. hda, hdb, hdc...
  if ($target_bus == "ide"){
    $ide_array = array();
    for ($i = 'a'; $i < 'z'; $i++)
      $ide_array[] = "hd" . $i;

    $tmp = libvirt_domain_get_disk_devices($dom);
    $result = array_intersect($ide_array,$tmp);
    if (count($result) > 0 ) {
      $highestresult = max($result);
      $target_dev = ++$highestresult;
    } else {
      $target_dev = "hda";
    }
  }

  //If $target_bus type is scsi or sata then we need to determine highest assigned value of drive, ex. sda, sdb, sdc...
  if ($target_bus == "sata" || $target_bus == "scsi"){
    $sd_array = array();
    for ($i = 'a'; $i < 'z'; $i++)
      $sd_array[] = "sd" . $i;

    $tmp = libvirt_domain_get_disk_devices($dom);
    $result = array_intersect($sd_array,$tmp);
    if (count($result) > 0 ) {
      $highestresult = max($result);
      $target_dev = ++$highestresult;
    } else {
      $target_dev = "sda";
    }
  }

  //add the new disk to domain if selected
  if ($source_file == "new") {
    $img = libvirt_storagevolume_get_path($new_disk);
    $dev = $target_dev;
    $typ = $target_bus;
    $driver = $driver_type;
    $ret = $lv->domain_disk_add($dom, $img, $dev, $typ, $driver);
  }

  //add an existing disk to domain if selected
  if ($source_file != "new") {


  $ret = $lv->domain_disk_add($dom, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  }

  unset($_SESSION['source_file']);
  unset($_SESSION['new_volume_name']);
  unset($_SESSION['new_volume_size']);
  unset($_SESSION['new_unit']);
  unset($_SESSION['new_volume_size']);
  unset($_SESSION['new_driver_type']);
  unset($_SESSION['new_target_bus']);
  unset($_SESSION['existing_target_bus']);

  //Return back to the orignal web page
  header('Location: ' . "domain-single.php?uuid=$uuid");
  exit;
}


require('../navbar.php');

?>
<script>
function diskChangeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;
    if (selectedValue.charAt(0) === "/") {
      selectedValue = "existing";
    }
  let subForms = document.getElementsByClassName('diskChange')
  for (let i = 0; i < subForms.length; i += 1) {
    if (selectedValue === subForms[i].id) {
      subForms[i].setAttribute('style', 'display:block')
    } else {
      subForms[i].setAttribute('style', 'display:none')
    }
  }
}


function newExtenstion(f) {
  var diskName = f.new_volume_name.value;
  diskName = diskName.replace(/\s+/g, '');
  var n = diskName.lastIndexOf(".");
  var noExt = n > -1 ? diskName.substr(0, n) : diskName;
  var driverType = f.new_driver_type.value;
  if (driverType === "qcow2"){
    var ext = ".qcow2";
    var fullDiskName = noExt.concat(ext);
    f.new_volume_name.value = fullDiskName;
  }
  if (driverType === "raw"){
    var ext = ".img";
    var fullDiskName = noExt.concat(ext);
    f.new_volume_name.value = fullDiskName;
  }
}

</script>

<div class="content">
  <div class="card">
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
    <div class="card-header">
      <h4 class="card-title"> New Storage Volume</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-4 col-6">
          <div class="nav-tabs-navigation verical-navs">
            <div class="nav-tabs-wrapper">
              <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#storage" role="tab" data-toggle="tab">Storage Volume</a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-md-7 col-sm-8 col-6">
          <!-- Tab panes -->
          <div class="tab-content">

            <div class="tab-pane active" id="storage">
              <div class="row">
                <label class="col-sm-2 col-form-label">Source File: </label>
                <div class="col-sm-7">
                  <div class="form-group">
                    <select onchange="diskChangeOptions(this)"  class="form-control" name="source_file">
                      <option value="none"> Select Disk </option>
                      <option value="new"> Create New Disk Image </option>
                      <?php
                      $pools = $lv->get_storagepools();
                      for ($i = 0; $i < sizeof($pools); $i++) {
                        $info = $lv->get_storagepool_info($pools[$i]);
                        if ($info['volume_count'] > 0) {
                          $tmp = $lv->storagepool_get_volume_information($pools[$i]);
                          $tmp_keys = array_keys($tmp);
                          for ($ii = 0; $ii < sizeof($tmp); $ii++) {
                            $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
                            $ext = pathinfo($tmp_keys[$ii], PATHINFO_EXTENSION);
                            if (strtolower($ext) != "iso")
                              echo "<option value='" . $tmp[$tmp_keys[$ii]]['path'] . "'>" . $tmp[$tmp_keys[$ii]]['path'] . "</option>";
                          }
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <label class="col-sm-2 col-form-label diskChange" id="new" style="display:none;">Volume Name: </label>
                <div class="col-sm-7">
                  <div class="form-group diskChange" id="new" style="display:none;">
                    <input type="text" class="form-control" id="DataImageName" value="newVM.qcow2" placeholder="Enter new disk name" name="new_volume_name">
                  </div>
                </div>
              </div>

              <div class="row">
                <label class="col-sm-2 col-form-label diskChange" id="new" style="display:none;">Volume Size: </label>
                <div class="col-sm-7">
                  <div class="form-group diskChange" id="new" style="display:none;">
                    <input type="number" class="form-control" value="40" min="1" name="new_volume_size">
                  </div>
                </div>
              </div>

              <div class="row">
                <label class="col-sm-2 col-form-label diskChange" id="new" style="display:none;">Memory Unit: </label>
                <div class="col-sm-7">
                  <div class="form-group diskChange" id="new" style="display:none;">
                    <div id="new_unit" class="btn-group" data-toggle="buttons">
                      <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                        <input type="radio" name="new_unit" value="M"> MB
                      </label>
                      <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-primary active">
                        <input type="radio" name="new_unit" value="G" checked="checked"> GB
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <label class="col-sm-2 col-form-label diskChange" id="new" style="display:none;">Driver Type: </label>
                <div class="col-sm-7">
                  <div class="form-group diskChange" id="new" style="display:none;">
                    <select class="form-control" onchange="newExtenstion(this.form)" name="new_driver_type">
                      <option value="qcow2" selected="selected">qcow2</option>
                      <option value="raw">raw</option>
                    </select>
                  </div>
                </div>
              </div>

            </div>






          </div>
        </div>

      </div>
    </div>
    <div class="card-footer text-right">
      <button type="submit" class="btn btn-danger">Create</button>
    </div>
    </form>
  </div>
</div>

<?php
require('../footer.php');
?>
