<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
//if (!isset($_SESSION['username'])){
//  header('Location: login.php');
//}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_GET['action']) || isset($_GET['dev']) || isset($_GET['mac']) || isset($_GET['snapshot'])) {
    $_SESSION['action'] = $_GET['action'];
    $_SESSION['dev'] = $_GET['dev'];
    $_SESSION['mac'] = $_GET['mac'];
    $_SESSION['snapshot'] = $_GET['snapshot'];
    $_SESSION['xmldesc'] = $_POST['xmldesc'];
    header("Location: ".$_SERVER['PHP_SELF']."?uuid=".$_GET['uuid']);
    exit;
}

// Add the header information
require('../header.php');

//Set variables
$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($uuid);
$dom = $lv->get_domain_object($domName);
$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];
$page = basename($_SERVER['PHP_SELF']);
$action = $_SESSION['action'];
$domXML = new SimpleXMLElement($lv->domain_get_xml($domName));


// Domain Actions
if ($action == 'domain-start') {
  $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
}

if ($action == 'domain-pause') {
  $ret = $lv->domain_suspend($domName) ? "Domain has been paused successfully" : 'Error while pausing domain: '.$lv->get_last_error();
}

if ($action == 'domain-resume') {
  $ret = $lv->domain_resume($domName) ? "Domain has been resumed successfully" : 'Error while resuming domain: '.$lv->get_last_error();
}

if ($action == 'domain-stop') {
  $ret = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
  $actioninfo = $lv->domain_get_info($dom);
  $actionstate = $lv->domain_state_translate($actioninfo['state']);
  if ($actionstate == "running"){
    $ret = "Domain is unable to shutdown gracefully. It may need to be forcefully shutdown";
  }
}

if ($action == 'domain-destroy') {
  $ret = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
}

if ($action == 'domain-delete') {
  $ret = $lv->domain_undefine($domName) ? "" : 'Error while deleting domain: '.$lv->get_last_error();
  if (!$lv->domain_get_name_by_uuid($uuid))
    header('Location: domain-list.php');
}


//Disk Actions
if ($action == 'domain-disk-remove') {
  $dev = $_SESSION['dev'];
  //Using XML to remove a disk, $ret = $lv->domain_disk_remove($domName, $dev) was not working correctly
  $path = $domXML->xpath('//disk');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->disk[$i]->target[dev] == $dev)
      unset($domXML->devices->disk[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $ret = $lv->domain_change_xml($domName, $newXML) ? 'Disk has been removed successfully' : 'Cannot remove disk: '.$lv->get_last_error();
  }
}


//Network Actions
if ($action == 'domain-nic-remove') {
  $mac = base64_decode($_SESSION['mac']);
  //Using XML to remove network, $ret = $lv->domain_nic_remove($domName, $mac) was not working correctly
  $path = $domXML->xpath('//interface');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->interface[$i]->mac[address] == $mac)
      unset($domXML->devices->interface[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $ret = $lv->domain_change_xml($domName, $newXML) ? 'Network interface has been removed successfully' : 'Cannot remove network interface: '.$lv->get_last_error();
  }
}


//Snapshot Actions
if ($action == 'domain-snapshot-create') {
  $ret = $lv->domain_snapshot_create($domName) ? "Snapshot for $domName successfully created" : 'Error while taking snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-delete') {
  $snapshot = $_SESSION['snapshot'];
  $ret = $lv->domain_snapshot_delete($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully deleted" : 'Error while deleting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-revert') {
  $snapshot = $_SESSION['snapshot'];
  $ret = $lv->domain_snapshot_revert($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully applied" : 'Error while reverting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-xml') {
  $snapshot = $_SESSION['snapshot'];
  $snapshotxml = $lv->domain_snapshot_get_xml($domName, $snapshot);
  //Parsing the snapshot XML file - in Ubuntu requires the php-xml package
  $xml = simplexml_load_string($snapshotxml);
  //Alternative way to parse
  //$xml = new SimpleXMLElement($snapshotxml);
}

if ($action == 'domain-edit') {
  $xml = $_SESSION['xmldesc'];
    $ret = $lv->domain_change_xml($domName, $xml) ? "Domain definition has been changed" : 'Error changing domain definition: '.$lv->get_last_error();
}


//get info, mem, cpu, state, id, arch, and vnc after actions to reflect any changes to domain
//Didn't use $info = $lv->domain_get_info($dom); because of caches state.
$info = libvirt_domain_get_info($dom);
$mem = number_format($info['memory'] / 1048576, 2, '.', ' ').' GB';
$cpu = $info['nrVirtCpu'];
$state = $lv->domain_state_translate($info['state']);
$id = $lv->domain_get_id($dom);
$arch = $lv->domain_get_arch($dom);
$vnc = $lv->domain_get_vnc_port($dom);

if (!$id)
  $id = 'N/A';
if ($vnc <= 0)
	$vnc = 'N/A';

require('../navbar.php');


// Setting up VNC connection information. tokens.list needs to have www-data ownership or 777 permissions
$liststring = "";
$listarray = $lv->get_domains();
foreach ($listarray as $listname) {
  $listdom = $lv->get_domain_object($listname);
  $listinfo = libvirt_domain_get_info($listdom);
  //Don't use $lv->domain_get_info($listdom) because the state is cached and caused delayed state status
  $liststate = $lv->domain_state_translate($listinfo['state']);
  if ($liststate == "running") {
    $listdomuuid = libvirt_domain_get_uuid_string($listdom);
    $listvnc = $lv->domain_get_vnc_port($listdom);
    $liststring = $liststring . $listdomuuid . ": " . "localhost:" . $listvnc . "\n";
  }
}
$listfile = "../../tokens.list";
$list = file_put_contents($listfile, $liststring);


//Remove session variables so that if page reloads it will not perform actions again
unset($_SESSION['action']);
unset($_SESSION['dev']);
unset($_SESSION['mac']);
unset($_SESSION['snapshot']);
unset($_SESSION['xmldesc']);


//Will display a sweet alert if a return message exists
if ($ret != "") {
echo "
<script>
var alert_msg = '$ret'
swal(alert_msg);
</script>";
}

//End PHP Section
?>

<script>
function domainDeleteWarning(linkURL) {
  swal("Are you sure you want to delete the domain?", {
    buttons: ["Cancel", true],
  }).then((value) => {
    if (value == true){
    // Redirect the user
    window.location = linkURL;
  }

  });
  }
</script>



<div class="content">
  <div class="card">
    <div class="row">
      <div class="col-md-4">
        <div class="card-header">
          <h4 class="card-title"> Virtual Machine: <?php echo $domName; ?></h4>
        </div>
        <div class="card-body">
          <?php
          if ($state == "running") {
            //Lets get the screenshot of the running domain
            $screenshot = $lv->domain_get_screenshot($uuid);
            //the raw png data needs to be encoded to use with html img tag
            $screen64 = base64_encode($screenshot['data']);
            ?>
            <a href="<?php echo $url; ?>:6080/vnc_lite.html?path=&token=<?php echo $uuid ?>" target="_blank">
            <img src="data:image/png;base64,<?php echo $screen64; ?>" width="100%"/>
            </a>
            <?php
          } else if ($state == "paused") {
            echo "<img src='../assets/img/paused.png' width='100%' >";
          } else {
            echo "<img src='../assets/img/shutdown.png' width='100%' >";
          }
          ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
