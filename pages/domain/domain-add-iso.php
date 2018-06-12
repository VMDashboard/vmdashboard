<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

require('../header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);
$domXML = new SimpleXMLElement($lv->domain_get_xml($domName));
$os_platform = $domXML->description;


// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $_SESSION['source_file'] = $_POST['source_file']; //determines if none, new, or existing disk is added

  header("Location: ".$_SERVER['PHP_SELF']."?uuid=".$uuid);
  exit;
}

if (isset($_SESSION['source_file'])) {
  $disk_type = "file";
  $disk_device = "cdrom";
  $driver_name = "qemu"; //not used
  $source_file = $_SESSION['source_file']; //determines if none, new, or existing disk is added
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

  //add a new cdrom XML
  $disk = $domXML->devices->addChild('disk');
  $disk->addAttribute('type','file');
  $disk->addAttribute('device','cdrom');

  $driver = $disk->addChild('driver');
  $driver->addAttribute('name','qemu');
  $driver->addAttribute('type','raw');

  $source = $disk->addChild('source');
  $source->addAttribute('file',$source_file);

  $target = $disk->addChild('target');
  $target->addAttribute('dev',$target_dev);
  $target->addAttribute('bus',$target_bus);

  $newXML = $domXML->asXML();
  $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);

  $ret = $lv->domain_change_xml($domName, $newXML); //third param is flags

  unset($_SESSION['source_file']);

  //Return back to the domain-single page
  header("Location: domain-single.php?uuid=".$uuid);
  exit;
}

require('../navbar.php');

?>

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Add ISO image to <?php echo $domName; ?></h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#optical" role="tab" data-toggle="tab">Optical Storage</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="optical">

                <div class="row">
                  <label class="col-sm-2 col-form-label">ISO File: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
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
