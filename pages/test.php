<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  //redirct to login page now
  header('Location: login.php');
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
require('header.php');

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

require('navigation.php');


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
$listfile = "../tokens.list";
$list = file_put_contents($listfile, $liststring);


//Remove session variables so that if page reloads it will not perform actions again
unset($_SESSION['action']);
unset($_SESSION['dev']);
unset($_SESSION['mac']);
unset($_SESSION['snapshot']);
unset($_SESSION['xmldesc']);


if ($ret) {
  ?>
  <script>
    var alertRet = "<?php echo $ret; ?>";
    swal(alertRet);
  </script>
  <?php
}

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


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3><a href="?uuid=<?php echo $uuid; ?>">Domain: <?php echo $domName; ?></a></h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile">
          <div class="x_title">
            <h2>Console</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
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

            <div class="col-xs-12 bottom text-center">

              <?php  if ($state == "running") { ?>
                <a href="<?php echo $url; ?>:6080/vnc.html?path=?token=<?php echo $uuid; ?>" target="_blank" >
                  <i class="fa fa-desktop"></i> Connect using noVNC |
                </a>
              <?php } ?>

              <?php if ($state == "shutoff") { ?>
                <a href="?action=domain-start&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-power-off"><br /></i> Power on |
                </a>
              <?php } ?>

              <?php  if ($state == "running") { ?>
                <a href="?action=domain-stop&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                <i class="fa fa-power-off"></i> Shutdown |
              </a>
                <a href="?action=domain-pause&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-pause"></i> Pause domain
                </a>
              <?php } ?>

              <?php  if ($state == "paused") { ?>
                <a href="?action=domain-resume&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-play"></i> Resume domain
                </a>
              <?php } ?>

              <?php  if ($state != "shutoff") { ?>
                <a href="?action=domain-destroy&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plug"></i> Forcefully shutdown |
                </a>
              <?php } ?>

              <?php  if ($state == "shutoff") { ?>
                <a onclick="domainDeleteWarning('?action=domain-delete&amp;uuid=<?php echo $uuid; ?>')" href="#" >
                  <i class="fa fa-trash"></i> Delete domain
                </a>
              <?php } ?>

            </div>

          </div>
        </div>
      </div>




    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile">
          <div class="x_title">
            <h2>General Information</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">

            <?php
            /* General information */
            echo "<table class=\"table\">";
              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1; border-top:none;\"><strong>Type: </strong></td>";
                echo "<td style=\"line-height:1; border-top:none;\">" . $lv->get_domain_type($domName) . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>Emulator: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $lv->get_domain_emulator($domName) . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>Memory: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $mem . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>vCPUs: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $cpu . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>State: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $state . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>Architecture: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $arch . "</td>";
              echo "</tr>";

              echo "<tr style=\"line-height:1;\">";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>ID: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $id . "</td>";
              echo "</tr>";

              echo "<tr>";
                echo "<td style=\"background: #f3f3f3; line-height:1;\"><strong>VNC Port: </strong></td>";
                echo "<td style=\"line-height:1; \">" . $vnc . "</td>";
              echo "</tr>";

            echo "</table>";

            if ($die)
              die('</body></html');
              ?>

            </div>
          </div>
        </div>




      <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile">
          <div class="x_title">
            <h2>Actions</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="dashboard-widget-content">
              <ul class="list-unstyled project_files">

                <?php  if ($state == "running") { ?>
                  <li><a href="<?php echo $url; ?>:6080/vnc.html?path=?token=<?php echo $uuid; ?>" target="_blank" >
                    <i class="fa fa-desktop"></i> Connect using noVNC<br />
                  </a></li>
                <?php } ?>

                <?php if ($state == "shutoff") { ?>
                  <li><a href="?action=domain-start&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-power-off"></i> Power on<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "running") { ?>
                  <li><a href="?action=domain-stop&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-power-off"></i> Shutdown<br />
                </a></li>
                  <li><a href="?action=domain-pause&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-pause"></i> Pause domain <br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "paused") { ?>
                  <li><a href="?action=domain-resume&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-play"></i> Resume domain <br />
                  </a></li>
                <?php } ?>

                <?php  if ($state != "shutoff") { ?>
                  <li><a href="?action=domain-destroy&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-plug"></i> Forcefully shutdown<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "shutoff") { ?>
                  <li><a onclick="domainDeleteWarning('?action=domain-delete&amp;uuid=<?php echo $uuid; ?>')" href="#" >
                    <i class="fa fa-trash"></i> Delete domain <br />
                  </a></li>
                <?php } ?>

                <hr>

                <li><a href="domain-add-disk.php?action=domain-disk-add&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add storage volume<br />
                </a></li>

                <li><a href="domain-add-iso.php?action=domain-disk-add&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add optical storage<br />
                </a></li>

                <li><a href="domain-add-network.php?uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add network adapter<br />
                </a></li>

                <li><a href="?action=domain-snapshot-create&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Create new snapshot<br />
                </a></li>

              </ul>
              <!-- end of actions -->

            </div>
          </div>
        </div>
      </div>





      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel fixed_height_320" style="overflow-y: scroll;" >
          <div class="x_title">
            <h2>Devices</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">

            <div class="col-xs-3">
              <!-- required for floating -->
              <!-- Nav tabs -->
              <ul class="nav nav-tabs tabs-left">
                <li class="active"><a href="#volumes" data-toggle="tab">Storage Volumes</a></li>
                <li><a href="#optical" data-toggle="tab">Optical Storage</a></li>
                <li><a href="#networking" data-toggle="tab">Networking Adapers</a></li>
              </ul>
            </div>

            <div class="col-xs-9">
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane active" id="volumes">
                  <?php
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
                      "<th>Physical disk size</th>" .
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
                        "<td>".basename($dev)."</td>" .
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
                    echo "<hr><p>Domain doesn't have any disk devices</p>";
                  }
                  ?>
                </div>

                <div class="tab-pane" id="optical">
                  <?php
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
                          "<td>$disk_source_file</td>" .
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
                    echo '<hr><p>Domain doesn\'t have any optical devices</p>';
                  }
                  ?>
                </div>
                <div class="tab-pane" id="networking">
                  <?php
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
                          "<td>$source_network</td>" .
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
                    echo '<hr><p>Domain doesn\'t have any network devices</p>';
                  }
                  ?>

                </div>
              </div>
            </div>

            <div class="clearfix"></div>

          </div>
        </div>
      </div>





      <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile fixed_height_320" style="overflow-y: scroll;">
          <div class="x_title">
            <h2>Snapshots</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
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
              echo "<hr><p>Domain does not have any snapshots</p>";
            }

            if ($snapshotxml != null) {
              echo "<hr>";
              echo "<h3>Snapshot XML: " . $snapshot . "</h3>";
              echo  "<textarea rows=15 style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\">" . $snapshotxml . "</textarea>";
            }
            ?>


          </div>
        </div>
      </div>





      <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile">
          <div class="x_title">
            <h2>Domain XML <?php if ($state != "shutoff"){ echo "(Read Only)"; } ?></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <?php
            /* XML information */
            $inactive = (!$lv->domain_is_running($domName)) ? true : false;
            $xml = $lv->domain_get_xml($domName, $inactive);
            $ret = htmlentities($xml);


            if ($state == "shutoff"){
              $ret = "<form method=\"POST\" action=?action=domain-edit&amp;uuid=" . $uuid . " >" .
                "<textarea name=\"xmldesc\" rows=\"17\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\" >" . $xml . "</textarea>" .
                "<br /> <br /> <input type=\"submit\" value=\"Save XML\"></form>";
              echo $ret;
            } else {
              echo "<textarea rows=\"17\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\" readonly>" . $ret . "</textarea>";
            }
            ?>
          </div>
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
