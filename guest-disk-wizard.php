<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);

//Grab post infomation and add new drive
if (isset($_POST['finish'])) {
  $disk_type_vda = "file";
  $disk_device_vda = "disk";
  $driver_name = "qemu"; //not used
  $driver_type = $_POST['driver_type'];
  $source_file = $_POST['source_file'];
  $target_dev = ""; //changed to an autoincremting option below.
  $target_bus = $_POST['target_bus'];
  $original_page = $_POST['original_page'];

  //If bus type is virtio then we need to determine highest assigned value of drive, ex. vda, vdb, vdc...
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

  //If bus type is ide then we need to determine highest assigned value of drive, ex. hda, hdb, hdc...
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

  //Add the new disk now
  $ret = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();

  //Return back to the orignal web page
  header('Location: ' . $original_page);
  exit;
}

require('navbar.php');
?>

<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post">
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new storage</h3>
            <h5 class="description">This form will allow you to add a new disk image.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab"><i class="fas fa-database"></i>Storage</a>
                </li>
              </ul>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!--    Storage Tab     -->
              <div class="tab-pane fade" id="storage">
                <h5 class="info-text"> Hard Drive Storage </h5>
                <div class="row justify-content-center">

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Disk drive source file location</label>
                      <select class="selectpicker" data-size="3" data-style="btn btn-plain btn-round" name="source_file">
                        <option value="none">Select File</option>
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

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Driver type</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="driver_type">
                        <option value="qcow2" selected="selected"> qcow2 </option>
                        <option value="raw"> raw </option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Target bus</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="target_bus">
                        <option value="virtio" selected="selected"> virtio </option>
                        <option value="ide"> ide </option>
                      </select>
                    </div>
                  </div>

                  <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" name="original_page">

                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="pull-right">
              <input type='submit' class='btn btn-finish btn-fill btn-rose btn-wd' name='finish' value='Finish' />
            </div>
            <div class="pull-left">
            </div>
            <div class="clearfix"></div>
          </div>
        </form>
      </div>
    </div> <!-- wizard container -->
  </div>
</div>
<?php
require('footer.php');
?>
