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
  $_SESSION['pool'] = $_POST['pool'];
  unset($_POST);
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if (isset($_SESSION['os'])) {
  $os = $_SESSION['os'];
  $pool = $_SESSION['pool'];
  unset($_SESSION['os']);
  unset($_SESSION['pool']);

  switch ($os) {
    case "ubuntu-18.04-live-server-amd64.iso":
        $download_link = "http://releases.ubuntu.com/18.04/ubuntu-18.04-live-server-amd64.iso";
        break;
    case "ubuntu-16.04.4-server-amd64.iso":
        $download_link = "http://releases.ubuntu.com/16.04.4/ubuntu-16.04.4-server-amd64.iso";
        break;
    default:
        $download_link = false;
}



/*

  //$ret = shell_exec("virsh -c qemu:///system list --all 2>&1");
  $size = exec("stat -Lc%s ubuntu-18.04-live-server-amd64.iso");
  //$tmp = exec("virsh -c qemu:///system vol-create-as default ubuntu_server.iso {$size} --format raw");

  $pool = "default";
  $volume_image_name = "ubuntu-18.04-live-server-amd64.iso";
  $volume_capacity = $size;
  $unit = "B";
  $volume_size = $size;
  $driver_type = "raw";

  $tmp = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';


  $ret = shell_exec("virsh -c qemu:///system vol-upload --pool default ubuntu-18.04-live-server-amd64.iso ubuntu-18.04-live-server-amd64.iso 2>&1");


  //$ret = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  //$msg = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';

  unset($_SESSION['pool']);

  //header('Location: storage-pools.php');
  //exit;
  */
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

<script>
function updateProgress(percentage) {
    document.getElementById("progress").value = percentage;
}
</script>


<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Upload ISO image</h4>
        <progress id="prog" value="0" max="100.0"></progress>
        <?php echo "<pre>" . $ret . "</pre>"; ?>
        <?php var_dump($tmp); ?>
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
                  <label class="col-sm-2 col-form-label">Operating System: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select  class="form-control" name="os">
                        <option value="ubuntu-18.04-live-server-amd64.iso">ubuntu-18.04-live-server-amd64.iso</option>
                        <option value="ubuntu-16.04.4-server-amd64.iso">ubuntu-16.04.4-server-amd64.iso</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Pool: </label>
                  <div class="col-sm-7">
                    <select  class="form-control" name="pool">
                      <option value="default" selected>default</option>
                    </select>
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


if ($download_link != false) {


//output buffer
ob_start();
//create javascript progress bar
/*
echo '<html><head>
<script type="text/javascript">
function updateProgress(percentage) {
    document.getElementById(\'progress\').value = percentage;
}
</script></head><body>
    <progress id="prog" value="0" max="100.0"></progress>
';
*/
//initilize progress bar
ob_flush();
flush();
//save progress to variable instead of a file
$temp_progress = '';
$targetFile = fopen( $os, 'w' );
$ch = curl_init( $download_link );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
curl_setopt( $ch, CURLOPT_FILE, $targetFile );
curl_exec( $ch );
fclose( $targetFile );
//must add $resource to the function after a newer php version. Previous comments states php 5.5
function progressCallback( $resource, $download_size, $downloaded_size, $upload_size, $uploaded_size )
{
    static $previousProgress = 0;

    if ( $download_size == 0 ) {
        $progress = 0;
    } else {
        $progress = round( $downloaded_size * 100 / $download_size );
	}

    if ( $progress > $previousProgress)
    {
        $previousProgress = $progress;
        $temp_progress = $progress;
    }
    //update javacsript progress bar to show download progress
	echo '<script>document.getElementById(\'prog\').value = '.$progress.';</script>';

	ob_flush();
    flush();
    //sleep(1); // just to see effect
}
//if we get here, the download has completed
echo "Done";
//flush just to be sure
ob_flush();
flush();

$size = exec("stat -Lc%s {$os}"); //{} variable in exec command

$volume_image_name = $os;
$volume_capacity = $size;
$unit = "B";
$volume_size = $size;
$driver_type = "raw";

$tmp = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';

$ret = shell_exec("virsh -c qemu:///system vol-upload --pool {$pool} {$os} {$os} 2>&1");

}

?>
