<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);


//Grab post infomation and add new drive
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $disk_type = "file";
  $disk_device= "cdrom";
  $driver_name = "qemu"; //not used
  $source_file = $_POST['source_file']; //determines if none, new, or existing disk is added
  $driver_type = "raw";
  $target_bus = "ide";

  $target_dev = ""; //changed to an autoincremting option below.

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


  $ret = $lv->domain_disk_add($dom, $source_file, $target_dev, $target_bus, $driver_type) ? "ISO has been successfully added to the guest" : "Cannot add ISO to the guest: ".$lv->get_last_error();


  //Return back to the orignal web page
  header('Location: ' . "domain-single.php?uuid=$uuid");
  exit;
}

require('navigation.php');
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>ISO Wizard</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add new iso image</h2>
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
                  <label for="source_file" class="control-label col-md-3 col-sm-3 col-xs-12">ISO File</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select class="form-control" name="source_file">
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
                            if (strtolower($ext) == "iso")
                              echo "<option value='" . $tmp[$tmp_keys[$ii]]['path'] . "'>" . $tmp[$tmp_keys[$ii]]['path'] . "</option>";
                          }
                        }
                      }
                      ?>
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
