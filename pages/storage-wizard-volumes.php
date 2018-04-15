<?php
require('header.php');

function clean_name_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
//will redirect to guests.php. header() needs to be before navbar.php. Uses libvirst so has to be after header.php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $pool = $_POST['pool'];
  $volume_image_name = clean_name_input($_POST['volume_image_name']);
  $volume_capacity = $_POST['volume_size'];
  $volume_size = $_POST['volume_size'];
  $unit = $_POST['unit'];
  $driver_type = $_POST['driver_type'];
  $original_page = $_POST['original_page'];

  $ret = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  $msg = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';
  header('Location: ' . $original_page);
  exit;
}

require('navigation.php');
?>

<script>
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
              <h2>Create new storage volume</h2>
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
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="volume_image_name">Volume name <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" value="newVolume.qcow2" required="required" placeholder="Enter name for new volume image" class="form-control" name="volume_image_name" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="volume_size">Volume size <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="number" class="form-control" name="volume_size" min="1" value="40" required="required" />
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Unit size</label>
                    <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Unit Size" name="unit">
                      <option value="M">MB</option>
                      <option value="G" selected>GB</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-10">
                  <div class="form-group">
                    <label>Driver type</label>
                    <select class="selectpicker" onchange="newExtenstion(this.form)" data-style="btn btn-plain btn-round" title="Select volume type" name="driver_type">
                      <option value="qcow2" selected>qcow2</option>
                      <option value="raw">raw</option>
                    </select>
                  </div>
                </div>


                <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" name="original_page"/>
                <input type="hidden" value="<?php echo $_GET['pool']; ?>" name="pool"/>






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
