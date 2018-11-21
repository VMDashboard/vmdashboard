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

function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'));
    $key = "";
    for($i=0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $key;
}

//Set variables
$randomString = RandomString(100);
$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($uuid);
$dom = $lv->get_domain_object($domName);
$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];
$page = basename($_SERVER['PHP_SELF']);
$action = $_SESSION['action'];
$domXML = new SimpleXMLElement($lv->domain_get_xml($domName));
$autostart = ($lv->domain_get_autostart($dom)) ? "yes" : "no";


// Domain Actions
if ($action == 'domain-start') {
  $notification = $lv->domain_start($domName) ? "" : 'Error while starting domain: '.$lv->get_last_error();
}

if ($action == 'domain-pause') {
  $notification = $lv->domain_suspend($domName) ? "" : 'Error while pausing domain: '.$lv->get_last_error();
}

if ($action == 'domain-resume') {
  $notification = $lv->domain_resume($domName) ? "" : 'Error while resuming domain: '.$lv->get_last_error();
}

if ($action == 'domain-stop') {
  $notification = $lv->domain_shutdown($domName) ? "" : 'Error while stopping domain: '.$lv->get_last_error();
  // $actioninfo = $lv->domain_get_info($dom);
  // $actionstate = $lv->domain_state_translate($actioninfo['state']);
  // if ($actionstate == "running"){
  //   $notification = "Domain is unable to shutdown gracefully. It may need to be forcefully shutdown";
  // }
}

if ($action == 'domain-destroy') {
  $notification = $lv->domain_destroy($domName) ? "" : 'Error while destroying domain: '.$lv->get_last_error();
}

if ($action == 'domain-delete') {
  $notification = $lv->domain_undefine($domName) ? "" : 'Error while deleting domain: '.$lv->get_last_error();
  if (!$lv->domain_get_name_by_uuid($uuid))
    header('Location: domain-list.php');
}


//Disk Actions
if ($action == 'domain-disk-remove') {
  $dev = $_SESSION['dev'];
  //Using XML to remove a disk, $notification = $lv->domain_disk_remove($domName, $dev) was not working correctly
  $path = $domXML->xpath('//disk');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->disk[$i]->target[dev] == $dev)
      unset($domXML->devices->disk[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $notification = $lv->domain_change_xml($domName, $newXML) ? "" : 'Cannot remove disk: '.$lv->get_last_error();
  }
}


//Network Actions
if ($action == 'domain-nic-remove') {
  $mac = base64_decode($_SESSION['mac']);
  //Using XML to remove network, $notification = $lv->domain_nic_remove($domName, $mac) was not working correctly
  $path = $domXML->xpath('//interface');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->interface[$i]->mac[address] == $mac)
      unset($domXML->devices->interface[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $notification = $lv->domain_change_xml($domName, $newXML) ? "" : 'Cannot remove network interface: '.$lv->get_last_error();
  }
}


//Snapshot Actions
if ($action == 'domain-snapshot-create') {
  $notification = $lv->domain_snapshot_create($domName) ? "Snapshot for $domName successfully created" : 'Error while taking snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-delete') {
  $snapshot = $_SESSION['snapshot'];
  $notification = $lv->domain_snapshot_delete($domName, $snapshot) ? "" : 'Error while deleting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-revert') {
  $snapshot = $_SESSION['snapshot'];
  $notification = $lv->domain_snapshot_revert($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully applied" : 'Error while reverting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-xml') {
  $snapshot = $_SESSION['snapshot'];
  $snapshotxml = $lv->domain_snapshot_get_xml($domName, $snapshot);
  //Parsing the snapshot XML file - in Ubuntu requires the php-xml package
  $xml = simplexml_load_string($snapshotxml);
  //Alternative way to parse
  //$xml = new SimpleXMLElement($snapshotxml);
}

//Domain XML Changes
if ($action == 'domain-edit') {
  $xml = $_SESSION['xmldesc'];
    $notification = $lv->domain_change_xml($domName, $xml) ? "XML for $domName has been updated" : 'Error changing domain XML: '.$lv->get_last_error();
    $domName = $lv->domain_get_name_by_uuid($uuid); //If the name is changed in XML will need to get it again

}

//Domain AutoStart Change
if ($action == 'domain-set-autostart') {
    $val = ($autostart == "yes") ? null : 1; // null disables autostart, 1 enables it
    $notification = $lv->domain_set_autostart($dom, $val) ? "" : 'Error changing domain autostart: '.$lv->get_last_error();
    $autostart = ($lv->domain_get_autostart($dom)) ? "yes" : "no"; //Check status again to display status in general informaion
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
$phpinfo = '<?php header(\'Location: index.php\'); ?>' . "\n";
$listarray = $lv->get_domains();
foreach ($listarray as $listname) {
  $listdom = $lv->get_domain_object($listname);
  $listinfo = libvirt_domain_get_info($listdom);
  //Don't use $lv->domain_get_info($listdom) because the state is cached and caused delayed state s$
  $liststate = $lv->domain_state_translate($listinfo['state']);
  //Will only generate string for current VM
  if ($liststate == "running" && $uuid == libvirt_domain_get_uuid_string($listdom)) {
    //$listdomuuid = libvirt_domain_get_uuid_string($listdom);
    $listvnc = $lv->domain_get_vnc_port($listdom);
    //$liststring = $liststring . $listdomuuid . ": " . "localhost:" . $listvnc . "\n";
    $liststring = $randomString . ": " . "localhost:" . $listvnc . "\n";
  }
}
$filestring = $phpinfo . $liststring;
$listfile = "../../tokens.php";
$list = file_put_contents($listfile, $filestring);


//Remove session variables so that if page reloads it will not perform actions again
unset($_SESSION['action']);
unset($_SESSION['dev']);
unset($_SESSION['mac']);
unset($_SESSION['snapshot']);
unset($_SESSION['xmldesc']);

?>

<div class="content">

  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" id="xml_card" style="display:none;">
      <div class="card card-stats-left">
        <div class="card-header card-header-none card-header-icon">
          <div class="card-icon">
            <i class="material-icons">code</i>
          </div>
          <h3 class="card-title">XML Data </h3>
          <p class="card-category">Virtual Machine Configuration file</p>
        </div>
        <div class="card-body">
          <?php
          /* XML information */
          $inactive = (!$lv->domain_is_running($domName)) ? true : false;
          $xml = $lv->domain_get_xml($domName, $inactive);
          $xmlDisplay = htmlentities($xml);

          if ($state == "shutoff"){
            //Creating an editable div because it looks much better than a textarea.
            //When form is submitted JavaScript will take innerText from editable div and store it in hidden textarea
            echo "<div contenteditable id=\"xml_div\"><pre>" . $xmlDisplay . "</pre></div>";
            echo "<form method=\"POST\" action=?action=domain-edit&amp;uuid=" . $uuid . " onsubmit=\"return submitXML()\">";
            echo "<textarea name=\"xmldesc\" id=\"xml_textarea\" style=\"display:none;\"></textarea>";
            echo "<br />";
            echo"<input type=\"submit\" value=\"Save\">";
            echo "</form>";
          } else {
            echo "<div><pre>" . $xmlDisplay . "</pre></div>";
            echo "<br /><input type=\"button\" onclick=\"closeXML()\" value=\"Close\">";
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-success card-header-icon">
          <div class="card-icon">
            <i class="material-icons">desktop_mac</i>
          </div>
          <h3 class="card-title">Console</h3>
          <p class="card-category">
            <?php  if ($state == "running") { ?>
                <a href="../vnc.php?token=<?php echo $randomString; ?>" target="_blank">
                VNC Console </a> |
            <?php } ?>

            <?php if ($state == "shutoff") { ?>
                <a href="?action=domain-start&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                Power on </a> |
            <?php } ?>

            <?php  if ($state == "running") { ?>
                <a href="?action=domain-stop&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                Shutdown</a> |
                <a href="?action=domain-pause&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                Pause</a> |
            <?php } ?>

            <?php  if ($state == "paused") { ?>
                <a href="?action=domain-resume&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                Resume</a> |
            <?php } ?>

            <?php  if ($state != "shutoff") { ?>
                <a href="?action=domain-destroy&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                Power off</a>
            <?php } ?>

            <?php  if ($state == "shutoff") { ?>
                <a onclick="domainDeleteWarning('?action=domain-delete&amp;uuid=<?php echo $uuid; ?>', '<?php echo $domName; ?>')" href="#" >
                <!-- <a href="?action=domain-delete&amp;uuid=<?php echo $uuid; ?>" target="_self" > -->
                Delete</a>
            <?php } ?>
          </p>
        </div>
        <div class="card-body">
          <?php
          if ($state == "running") {
            //Lets get the vnc console preview of the running domain
            ?>
            <div style="position:relative; width: 295px; height: 221px; margin-left:auto; margin-right:0;">
              <iframe src="<?php echo $url; ?>:6080/vnc_screen.html?view_only=true&path=&scale=true&token=<?php echo $randomString ?>" style="width: 100%; height: 100%; border: none;"></iframe>
              <a href="../vnc.php?token=<?php echo $randomString; ?>" target="_blank"  style="position:absolute; top:0; left:0; display:inline-block; width:100%; height:100%; z-index:99;"></a>
            </div>
            <?php
          } else if ($state == "paused") {
            echo "<img src='../../assets/img/paused.png' width='295px' height='221px' >";
          } else {
            echo "<img src='../../assets/img/shutdown.png' width='295px' height='221px' >";
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-warning card-header-icon">
          <div class="card-icon">
            <i class="material-icons">phonelink</i>
          </div>
          <h3 class="card-title">Virtual Machine: <?php echo htmlentities($domName); ?> </h3>
          <p class="card-category"></p>
        </div>
        <div class="card-body">
          <?php
          echo "<strong>Type: </strong>" . $lv->get_domain_type($domName) . "<br />";
          echo "<strong>Emulator: </strong>" . $lv->get_domain_emulator($domName) . "<br />";
          echo "<strong>Memory: </strong>" . $mem . "<br />";
          echo "<strong>vCPUs: </strong>" . $cpu . "<br />";
          echo "<strong>State: </strong>" . $state . "<br />";
          echo "<strong>Architecture: </strong>" . $arch . "<br />";
          echo "<strong>ID: </strong>" . $id . "<br />";
          echo "<strong>VNC Port: </strong>" . $vnc . "<br />";
          echo "<strong>AutoStart: </strong>" . $autostart . " <a href=\"?action=domain-set-autostart&amp;uuid=" . $uuid . "\" target=\"_self\" > (Change) </a> <br />";
          echo "<strong>XML: </strong>";
            if ($state == "shutoff") {
              echo "<a href=\"#\" onclick=\"showXML()\"> Edit</a>";
            } else {
              echo "<a href=\"#\" onclick=\"showXML()\"> View</a>";
            }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-primary card-header-icon">
          <div class="card-icon">
            <i class="material-icons">storage</i>
          </div>
          <h3 class="card-title">Storage Volumes</h3>
          <p class="card-category">Hard Drive Files</p>
        </div>
        <div class="card-body">
          <?php
          if ($state == "shutoff"){
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "<a href=\"domain-add-volume.php?uuid=$uuid\" target=\"_self\" >";
            echo "Add storage volume</a> <br /> <br />";
          } else {
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "Power off to add storage volume<br /> <br />";
          }
          /* Disk information */
          $tmp = $lv->get_disk_stats($domName);
          if (!empty($tmp)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              "<tr>" .
              "<th>Volume</th>" .
              "<th>Driver</th>" .
              "<th>Device</th>" .
              "<th>Disk capacity</th>" .
              "<th>Disk allocation</th>" .
              "<th>Physical size</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              "<tbody>";
            for ($i = 0; $i < sizeof($tmp); $i++) {
              $capacity = $lv->format_size($tmp[$i]['capacity'], 2);
              $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
              $physical = $lv->format_size($tmp[$i]['physical'], 2);
              $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];
              $device = $tmp[$i]['device'];
              echo "<tr>" .
                "<td>".htmlentities(basename($dev))."</td>" .
                  "<td>{$tmp[$i]['type']}</td>" .
                  "<td>{$tmp[$i]['device']}</td>" .
                  "<td>$capacity</td>" .
                  "<td>$allocation</td>" .
                  "<td>$physical</td>" .
                  "<td>" .
                  "<a title='Remove' href=\"?action=domain-disk-remove&amp;dev=$device&amp;uuid=$uuid\">Remove</a>" .
                  "</td>" .
                  "</tr>";
            }
            echo "</tbody></table></div>";
          } else {
            echo "<hr><p>Guest does not have any disk devices</p>";
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-rose card-header-icon">
          <div class="card-icon">
            <i class="material-icons">album</i>
          </div>
          <h3 class="card-title">Optical Storage</h3>
          <p class="card-category">CD/DVD ISO files</p>
        </div>
        <div class="card-body">
          <?php
          if ($state == "shutoff"){
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "<a href=\"domain-add-iso.php?uuid=$uuid\" target=\"_self\" >";
            echo "Add optical storage</a> <br /> <br />";
          }else {
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "Power off to add optical storage<br /> <br />";
          }
          /* Optical device information */
          $path = $domXML->xpath('//disk');
          if (!empty($path)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              "<tr>" .
              "<th>ISO file</th>" .
              "<th>Driver</th>" .
              "<th>Device</th>" .
              "<th>Bus</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              "<tbody>";

            for ($i = 0; $i < sizeof($path); $i++) {
              //$disk_type = $domXML->devices->disk[$i][type];
              $disk_device = $domXML->devices->disk[$i][device];
              $disk_driver_name = $domXML->devices->disk[$i]->driver[name];
              //$disk_driver_type = $domXML->devices->disk[$i]->driver[type];
              $disk_source_file = $domXML->devices->disk[$i]->source[file];
              if (empty($disk_source_file)) {
                $disk_source_file = "empty";
              }
              $disk_target_dev = $domXML->devices->disk[$i]->target[dev];
              $disk_target_bus = $domXML->devices->disk[$i]->target[bus];

              if ($disk_device == "cdrom") {
                echo "<tr>" .
                  "<td>" . htmlentities($disk_source_file) . "</td>" .
                  "<td>$disk_driver_name</td>" .
                  "<td>$disk_target_dev</td>" .
                  "<td>$disk_target_bus</td>" .
                  "<td>" .
                    "<a title='Remove' href=\"?action=domain-disk-remove&amp;dev=$disk_target_dev&amp;uuid=$uuid\">Remove</a>" .
                  "</td>" .
                  "</tr>";
              }
            }
            echo "</tbody></table></div>";
          } else {
            echo '<hr><p>Guest does not have any optical devices</p>';
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-info card-header-icon">
          <div class="card-icon">
            <i class="material-icons">device_hub</i>
          </div>
          <h3 class="card-title">Network Interfaces </h3>
          <p class="card-category">NAT or Bridged Adapters</p>
        </div>
        <div class="card-body">
          <?php
          if ($state == "shutoff"){
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "<a href=\"domain-add-network.php?uuid=$uuid\" target=\"_self\" >";
            echo "Add network adapter</a> <br /> <br />";
          }else {
            echo "<i class=\"fa fa-plus\" style=\"padding-right:7px;\"></i>";
            echo "Power off to add network adapter<br /> <br />";
          }
          /* Network interface information */
          $path = $domXML->xpath('//interface');
          if (!empty($path)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              "<tr>" .
              "<th>Type</th>" .
              "<th>MAC Address</th>" .
              "<th>Source</th>" .
              "<th>Mode</th>" .
              "<th>Model</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              "<tbody>";

            for ($i = 0; $i < sizeof($path); $i++) {
              $interface_type = $domXML->devices->interface[$i][type];
              $interface_mac = $domXML->devices->interface[$i]->mac[address];
              $mac_encoded = base64_encode($interface_mac); //used to send via $_GET
              if ($interface_type == "network") {
                $source_network = $domXML->devices->interface[$i]->source[network];
              }
              if ($interface_type == "direct") {
                $source_dev = $domXML->devices->interface[$i]->source[dev];
                $source_mode = $domXML->devices->interface[$i]->source[mode];
              }
              $interface_model = $domXML->devices->interface[$i]->model[type];

              if ($interface_type == "network") {
                echo "<tr>" .
                  "<td>$interface_type</td>" .
                  "<td>$interface_mac</td>" .
                  "<td>" . htmlentities($source_network) . "</td>" .
                  "<td>nat</td>" .
                  "<td>$interface_model</td>" .
                  "<td>" .
                    "<a href=\"?action=domain-nic-remove&amp;uuid=$uuid&amp;mac=$mac_encoded\">" .
                    "Remove</a>" .
                  "</td>" .
                  "</tr>";
              }
              if ($interface_type == "direct") {
                echo "<tr>" .
                  "<td>$interface_type</td>" .
                  "<td>$interface_mac</td>" .
                  "<td>$source_dev</td>" .
                  "<td>$source_mode</td>" .
                  "<td>$interface_model</td>" .
                  "<td>" .
                    "<a href=\"?action=domain-nic-remove&amp;uuid=$uuid&amp;mac=$mac_encoded\">" .
                    "Remove</a>" .
                  "</td>" .
                  "</tr>";
              }
            }
            echo "</tbody></table></div>";
          } else {
            echo '<hr><p>Guest does not have any network devices</p>';
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats">
        <div class="card-header card-header-danger card-header-icon">
          <div class="card-icon">
            <i class="material-icons">file_copy</i>
          </div>
          <h3 class="card-title">Snapshots </h3>
          <p class="card-category">Saved States</p>
        </div>
        <div class="card-body">
          <i class="fa fa-plus" style="padding-right:7px;"></i>
          <a href="?action=domain-snapshot-create&amp;uuid=<?php echo $uuid; ?>" target="_self" >
          Create new snapshot</a> <br /> <br />
          <?php
          /* Snapshot information */
          $tmp = $lv->list_domain_snapshots($dom);
          if (!empty($tmp)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              "<tr>" .
              "<th>Name</th>" .
              "<th>Creation Time</th>" .
              "<th>Domain State</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              "<tbody>";

            foreach ($tmp as $key => $value) {
              //Getting XML info on the snapshot. Using simpleXLM because libvirt xml functions don't seem to work for snapshots
              $tmpsnapshotxml = $lv->domain_snapshot_get_xml($domName, $value);
              $tmpxml = simplexml_load_string($tmpsnapshotxml);
              $name = $tmpxml->name[0];
              $creationTime = $tmpxml->creationTime[0];
              $snapstate = $tmpxml->state[0];
              echo "<tr>";
              echo "<td>" . $name . "</td>";
              echo "<td>" . date("D d M Y", $value) . " - ";
              echo date("H:i:s", $value) . "</td>";
              echo "<td>" . $snapstate . "</td>";
              echo "<td>
                <a title='Delete snapshot' href=\"?action=domain-snapshot-delete&amp;snapshot=$value&amp;uuid=$uuid\">Delete | </a>
                <a title='Revert snapshot' href=?action=domain-snapshot-revert&amp;uuid=" . $uuid . "&amp;snapshot=" . $value . ">Revert | </a>
                <a title='View snapshot XML' href=?action=domain-snapshot-xml&amp;uuid=" . $uuid . "&amp;snapshot=" . $value . ">XML</a>
                </td>";
              echo "</tr>";
            }
            echo "</tbody></table></div>";
          } else {
            echo "<hr><p>Guest does not have any snapshots</p>";
          }

          if ($snapshotxml != null) {
            echo "<hr>";
            echo "<h3>Snapshot XML: " . $snapshot . "</h3>";
            echo  "<textarea rows=15 style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\">" . $snapshotxml . "</textarea>";
          }
          ?>
        </div>
        <div class="card-footer">
          <div class="stats">
          </div>
        </div>
      </div>
    </div>

  </div> <!-- End Row -->

</div> <!-- End Content -->

<script>
  window.onload =  function() {
    <?php
    if ($notification) {
      echo "showNotification(\"top\",\"right\",\"$notification\");";
    }
    ?>
  }

  function domainDeleteWarning(linkURL, domName) {
    let r = confirm("Deleting virtual machine " + domName + ".");
    if (r == true) {
      window.location = linkURL;
    }
  }

  function showXML() {
    let d = document.getElementById("xml_card");
    d.style.display = "block";
  }

  function closeXML() {
    let d = document.getElementById("xml_card");
    d.style.display = "none";
  }

  function submitXML() {
    let x = document.getElementById("xml_div").innerText;
    let y = document.getElementById("xml_textarea");
    y.value = x;
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
</script>

<?php
require('../footer.php');
?>
