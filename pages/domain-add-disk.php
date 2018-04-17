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
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
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


  $ret = $lv->domain_disk_add($dom, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  }

  //Return back to the orignal web page
  header('Location: ' . "domain-single.php?uuid=$uuid");
  exit;
}

require('navigation.php');
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


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Volume Wizard</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add new storage volume</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a></li>
                  <li><a href="#">Settings 2</a></li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">
            <!-- Smart Wizard -->
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post">
              <div class="form-horizontal form-label-left" style="min-height: 250px;">

                <div class="form-group">
                  <label for="source_file" class="control-label col-md-3 col-sm-3 col-xs-12">Disk source file</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select onchange="diskChangeOptions(this)" class="form-control" name="source_file">
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

                <div class="form-group diskChange" id="new" style="display:none;">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="volume_image_name">Disk name <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" value="newVM.qcow2" required="required" id="DataImageName" placeholder="Enter name for new disk image" class="form-control" name="new_volume_name" />
                  </div>
                </div>


                <div class="form-group diskChange" id="new" style="display:none;">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="new_volume_size">Volume size</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="number" value="40" class="form-control" name="new_volume_size" min="1" />
                  </div>
                </div>

                <div class="form-group diskChange" id="new" style="display:none;">
                  <label for="new_unit" class="control-label col-md-3 col-sm-3 col-xs-12">Unit Size</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select  class="form-control" name="new_unit">
                      <option value="M">MB</option>
                      <option value="G" selected>GB</option>
                    </select>
                  </div>
                </div>

                <div class="form-group diskChange" id="new" style="display:none;">
                  <label for="new_driver_type" class="control-label col-md-3 col-sm-3 col-xs-12">Driver type</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select  class="form-control" name="new_driver_type" onchange="newExtenstion(this.form)">
                      <option value="qcow2" selected="selected">qcow2</option>
                      <option value="raw" >raw</option>
                    </select>
                  </div>
                </div>

                <div class="form-group diskChange" id="new" style="display:none;">
                  <label for="new_target_bus" class="control-label col-md-3 col-sm-3 col-xs-12">Target bus</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select  class="form-control" name="new_target_bus" >
                      <option value="virtio" selected="selected">virtio</option>
                      <option value="ide">ide</option>
                    </select>
                  </div>
                </div>

                <div class="form-group diskChange" id="existing" style="display:none;">
                  <label for="existing_driver_type" class="control-label col-md-3 col-sm-3 col-xs-12">Driver type</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select  class="form-control" name="existing_driver_type" >
                      <option value="qcow2" selected="selected">qcow2</option>
                      <option value="raw">raw</option>
                    </select>
                  </div>
                </div>


                <div class="form-group diskChange" id="existing" style="display:none;">
                  <label for="existing_target_bus" class="control-label col-md-3 col-sm-3 col-xs-12">Target bus</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select  class="form-control" name="existing_target_bus" >
                      <option value="virtio" selected="selected">virtio</option>
                      <option value="ide">ide</option>
                    </select>
                  </div>
                </div>

              </div>

              <div class="actionBar">
                <input type="submit" name="submit" class="buttonFinish btn btn-default" value="Finish" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php
require('footer.php');
?>
