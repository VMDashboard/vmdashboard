<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);

function clean_name_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

//Grab post infomation and add new drive
if (isset($_POST['finish'])) {
  $original_page = $_POST['original_page'];
  $disk_type = "file";
  $disk_device= "disk";
  $driver_name = "qemu"; //not used
  $source_file = $_POST['source_file']; //determines if none, new, or existing disk is added

  if ($source_file == "new") {
    $pool = "default"; //using the default storage pool to store images
    $volume_image_name = clean_name_input($_POST['new_volume_name']); //the new name of the volume disk image
    $volume_capacity = $_POST['new_volume_size']; //number used for volume size
    $unit = $_POST['new_unit']; //determines MiB or GiB
    $volume_size = $_POST['new_volume_size'];
    $driver_type = $_POST['new_driver_type']; //qcow2 or raw
    //Create the new disk now
    $new_disk = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type);
    $target_bus = $_POST['new_target_bus']; //virtio or ide, used when adding disk to domain

  } elseif ($source_file == "none") {
    //
  } else {
    $driver_type = $_POST['existing_driver_type']; //qcow2 or raw
    $target_bus = $_POST['existing_target_bus']; //virtio or ide
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
    $target_dev = "vdg";
    $target_bus = "virtio";
    $driver_type = "qcow2";
  $ret = $lv->domain_disk_add($dom, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  }

  //Return back to the orignal web page
  header('Location: ' . $original_page);
  exit;
}

require('navbar.php');
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
</script>


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
                      <select onchange="diskChangeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="source_file">
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

                  <div class="col-sm-10 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Disk Image Name</label>
                      <input type="text" id="DataImageName" value="newVM.qcow2" placeholder="Enter new disk name" class="form-control" name="new_volume_name"/>
                    </div>
                  </div>

                  <div class="col-sm-6 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Volume size</label>
                      <input type="number" value="40" class="form-control" name="new_volume_size" min="1" />
                    </div>
                  </div>

                  <div class="col-sm-4 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Unit size</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Unit Size" name="new_unit">
                        <option value="M">MB</option>
                        <option value="G" selected>GB</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-10 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Driver type</label>
                      <select onchange="newExtenstion(this.form)" class="selectpicker" data-style="btn btn-plain btn-round" name="new_driver_type">
                        <option value="qcow2" selected="selected">qcow2</option>
                        <option value="raw" >raw</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-10 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Target bus</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="new_target_bus">
                        <option value="virtio" selected="selected">virtio</option>
                        <option value="ide">ide</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5 diskChange" id="existing" style="display:none;">
                    <div class="form-group">
                      <label>Driver type</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="existing_driver_type">
                        <option value="qcow2" selected="selected">qcow2</option>
                        <option value="raw">raw</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5 diskChange" id="existing" style="display:none;">
                    <div class="form-group">
                      <label>Target bus</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="existing_target_bus">
                        <option value="virtio" selected="selected">virtio</option>
                        <option value="ide">ide</option>
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
