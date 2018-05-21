<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);
$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];
$page = basename($_SERVER['PHP_SELF']);
$action = $_GET['action'];
$domXML = $lv->domain_get_xml($domName);
$domXML = new SimpleXMLElement($domXML);
//$domXML = new SimpleXMLElement($lv->domain_get_xml($domName));


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
    $ret = "Domain is unable to shutdown gracefully. It will need to be forcefully turned off";
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
  $dev = $_GET['dev'];
  //My XML way
  $path = $domXML->xpath('//disk');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->disk[$i]->target[dev] == $dev)
      unset($domXML->devices->disk[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $ret = $lv->domain_change_xml($domName, $newXML) ? 'Disk has been removed successfully' : 'Cannot remove disk: '.$lv->get_last_error();
  }

  //Suggested way, however not working
  //$ret = $lv->domain_disk_remove($domName, $dev) ? 'Disk has been removed successfully' : 'Cannot remove disk: '.$lv->get_last_error();
}

//Network Actions
if ($action == 'domain-nic-remove') {
  $mac = base64_decode($_GET['mac']);
  //My XML way
  $path = $domXML->xpath('//interface');
  for ($i = 0; $i < sizeof($path); $i++) {
    if ($domXML->devices->interface[$i]->mac[address] == $mac)
      unset($domXML->devices->interface[$i]);
      $newXML = $domXML->asXML();
      $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
      $ret = $lv->domain_change_xml($domName, $newXML) ? 'Network interface has been removed successfully' : 'Cannot remove network interface: '.$lv->get_last_error();
  }

  //Suggested way, however not working
  //$ret = $lv->domain_nic_remove($domName, $mac) ? "Network device successfully removed" : 'Error while removing network device: '.$lv->get_last_error();
}

//Snapshot Actions
if ($action == 'domain-snapshot-create') {
  $ret = $lv->domain_snapshot_create($domName) ? "Snapshot for $domName successfully created" : 'Error while taking snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-delete') {
  $snapshot = $_GET['snapshot'];
  $ret = $lv->domain_snapshot_delete($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully deleted" : 'Error while deleting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-revert') {
  $snapshot = $_GET['snapshot'];
  $ret = $lv->domain_snapshot_revert($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully applied" : 'Error while reverting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-xml') {
  $snapshot = $_GET['snapshot'];
  $snapshotxml = $lv->domain_snapshot_get_xml($domName, $snapshot);
  //Parsing the snapshot XML file - in Ubuntu requires the php-xml package
  $xml = simplexml_load_string($snapshotxml);
  //Alternative way to parse
  //$xml = new SimpleXMLElement($snapshotxml);
}

if ($action == 'domain-edit') {
  $xml = $_POST['xmldesc'];
    $ret = $lv->domain_change_xml($domName, $xml) ? "Domain definition has been changed" : 'Error changing domain definition: '.$lv->get_last_error();
}

//get info, mem, cpu, state, id, arch, and vnc after actions to reflect any changes to domain
$info = $lv->domain_get_info($dom);
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
  //Don't use $lv->domain_get_info($listdom) because the state is cached and caused delay state status
  $liststate = $lv->domain_state_translate($listinfo['state']);
  if ($liststate == "running") {
    $listdomuuid = libvirt_domain_get_uuid_string($listdom);
    $listvnc = $lv->domain_get_vnc_port($listdom);
    $liststring = $liststring . $listdomuuid . ": " . "localhost:" . $listvnc . "\n";
  }
}
$listfile = "../tokens.list";
$list = file_put_contents($listfile, $liststring);
?>

<?php
if ($ret) {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Domain Information</h3>
      </div>
    </div>

    <div class="clearfix"></div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320">
        <div class="x_title">
          <h2>Console</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="col-md-4 col-sm-4 col-xs-12 profile_left">
            <div class="profile_img">
              <div id="crop-avatar">
                <!-- Current avatar -->
                <?php
                if ($state == "running") {
                  //screenshot will get raw png data at 300 pixels wide
                  $screenshot = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], 400);
                  //the raw png data needs to be encoded to use with html img tag
                  $screen64 = base64_encode($screenshot['data']);
                  ?>
                  <a href="<?php echo $url; ?>:6080/vnc.html?path=?token=<?php echo $uuid ?>" target="_blank">
                  <img src="data:image/png;base64,<?php echo $screen64 ?>" width="300px"/>
                  </a>
                  <?php
                } else if ($state == "paused") {
                  echo "<img src='../assets/img/paused.png' width='300px' >";
                } else {
                  echo "<img src='../assets/img/shutdown.png' width='300px' >";
                }
                ?>
            <!--    <img class="img-responsive avatar-view" src="images/picture.jpg" alt="Avatar" title="Change the avatar"> -->
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>








      <div class="col-md-8 col-sm-8 col-xs-12" style="overflow-y: scroll;">
        <div class="x_panel tile fixed_height_320" style="overflow-y: scroll;">
          <div class="x_title" style="overflow-y: scroll;">
            <h2><a href="?uuid=<?php echo $uuid; ?>"><?php echo $domName; ?></a></h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a>
                  </li>
                  <li><a href="#">Settings 2</a>
                  </li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content" style="overflow-y: scroll;">
            <div class="col-md-4 col-sm-4 col-xs-12 profile_left" style="overflow-y: scroll;">
              <div class="profile_img">
                <div id="crop-avatar">
                  <!-- Current avatar -->
                  <?php
                  if ($state == "running") {
                    //screenshot will get raw png data at 300 pixels wide
                    $screenshot = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], 400);
                    //the raw png data needs to be encoded to use with html img tag
                    $screen64 = base64_encode($screenshot['data']);
                    ?>
                    <a href="<?php echo $url; ?>:6080/vnc.html?path=?token=<?php echo $uuid ?>" target="_blank">
                    <img src="data:image/png;base64,<?php echo $screen64 ?>" width="300px"/>
                    </a>
                    <?php
                  } else if ($state == "paused") {
                    echo "<img src='../assets/img/paused.png' width='300px' >";
                  } else {
                    echo "<img src='../assets/img/shutdown.png' width='300px' >";
                  }
                  ?>
              <!--    <img class="img-responsive avatar-view" src="images/picture.jpg" alt="Avatar" title="Change the avatar"> -->
                </div>
              </div>

              <!-- start actions -->
              <h4>Actions</h4>
              <ul class="list-unstyled user_data">

                <?php  if ($state == "running") { ?>
                  <li><a href="<?php echo $url; ?>:6080/vnc.html?path=?token=<?php echo $uuid; ?>" target="_blank" >
                    <i class="fa fa-desktop"></i> Connect to Domain using VNC<br />
                  </a></li>
                <?php } ?>

                <?php if ($state == "shutoff") { ?>
                  <li><a href="?action=domain-start&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-power-off"></i> Power guest domain on<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "running") { ?>
                  <li><a href="?action=domain-stop&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-power-off"></i> Power guest domain off gracefully<br />
                </a></li>
                  <li><a href="?action=domain-pause&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-pause"></i> Pause domain guest<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "paused") { ?>
                  <li><a href="?action=domain-resume&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-play"></i> Resume domain guest<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state != "shutoff") { ?>
                  <li><a href="?action=domain-destroy&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-plug"></i> Turn off domain guest forcefully<br />
                  </a></li>
                <?php } ?>

                <?php  if ($state == "shutoff") { ?>
                  <li><a href="?action=domain-delete&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                    <i class="fa fa-trash"></i> Delete domain guest<br />
                  </a></li>
                <?php } ?>


                <li><a href="domain-add-disk.php?action=domain-disk-add&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add new disk<br />
                </a></li>

                <li><a href="domain-add-iso.php?action=domain-disk-add&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add new iso image<br />
                </a></li>

                <li><a href="domain-add-network.php?uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Add new network<br />
                </a></li>

                <li><a href="?action=domain-snapshot-create&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fa fa-plus"></i> Create new snapshot<br />
                </a></li>

              </ul>
              <!-- end of actions -->
            </div>

            <!-- next column -->
            <div class="col-md-8 col-sm-8 col-xs-12">

              <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#tab_content1" id="general-tab" role="tab" data-toggle="tab" aria-expanded="true">General Info</a>
                  </li>
                  <li role="presentation" class=""><a href="#tab_content2" role="tab" id="xml-tab" data-toggle="tab" aria-expanded="false">XML Info</a>
                  </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                  <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="general-tab">
                    <?php
                    /* General information */
                    echo "<strong>Domain type: </strong>".$lv->get_domain_type($domName)."<br />";
                    echo "<strong>Domain emulator: </strong>".$lv->get_domain_emulator($domName)."<br />";
                    echo "<strong>Domain memory: </strong>$mem<br />";
                    echo "<strong>Number of vCPUs: </strong>$cpu<br />";
                    echo "<strong>Domain state: </strong>$state<br />";
                    echo "<strong>Domain architecture: </strong>$arch<br />";
                    echo "<strong>Domain ID: </strong>$id<br />";
                    echo "<strong>VNC Port: </strong>$vnc<br />";
                    if ($die)
                      die('</body></html');
                    ?>
                  </div>


                  <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="xml-tab">
                    <?php
                    /* XML information */
                    $inactive = (!$lv->domain_is_running($domName)) ? true : false;
                    $xml = $lv->domain_get_xml($domName, $inactive);
                    $ret = htmlentities($xml);


                    if ($state == "shutoff"){
                      $ret = "<form method=\"POST\" action=?action=domain-edit&amp;uuid=" . $_GET['uuid'] . " >" .
                        "<textarea name=\"xmldesc\" rows=\"17\" cols=\"2\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\" >" . $xml . "</textarea>" .
                        "<br /> <br /> <input type=\"submit\" value=\"Save XML\"></form>";
                      echo $ret;
                    } else {
                      echo "<p>*Editing XML is performed when virtual guest is shutoff</p>";
                      echo "<textarea rows=\"17\" cols=\"2\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;\" readonly>" . $ret . "</textarea>";
                    }
                    ?>
                  </div>
                </div>

              </div>

              <br/><br/>
              <!-- add storage here -->
              <h4>Disk Information</h4>
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
                      "<a title='Remove disk device' href=\"?action=domain-disk-remove&amp;dev=$device&amp;uuid=$uuid\">Remove disk</a>" .
                      "</td>" .
                      "</tr>";
                }
                echo "</tbody></table></div>";
              } else {
                echo "<hr><p>Domain doesn't have any disk devices</p>";
              }
              ?>


              <br/><br/><br/>
              <!-- add network here -->
              <h4>Optical Device Information</h4>
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
                        "<a title='Remove cdrom device' href=\"?action=domain-disk-remove&amp;dev=$disk_target_dev&amp;uuid=$uuid\">Remove</a>" .
                      "</td>" .
                      "</tr>";
                  }
                }
                echo "</tbody></table></div>";
              } else {
                echo '<hr><p>Domain doesn\'t have any optical devices</p>';
              }
              ?>


              <br/><br/><br/>
              <!-- add network here -->
              <h4>Network Information</h4>
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
                        "Remove interface</a>" .
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
                        "Remove interface</a>" .
                      "</td>" .
                      "</tr>";
                  }
                }
                echo "</tbody></table></div>";
              } else {
                echo '<hr><p>Domain doesn\'t have any network devices</p>';
              }
              ?>



              <br/><br/><br/>
              <!-- add snapshot here -->
              <h4>Snapshot Information</h4>
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
                    <a title='Revert snapshot' href=?action=domain-snapshot-revert&amp;uuid=" . $_GET['uuid'] . "&amp;snapshot=" . $value . ">Revert | </a>
                    <a title='View snapshot XML' href=?action=domain-snapshot-xml&amp;uuid=" . $_GET['uuid'] . "&amp;snapshot=" . $value . ">XML</a>
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
                echo  "<textarea rows=15 cols=50>" . $snapshotxml . "</textarea>";
              }
              ?>

            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile fixed_height_320">
          <div class="x_title">
            <h2>Console</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="dashboard-widget-content">
              <ul class="quick-list">
                <li><i class="fa fa-calendar-o"></i><a href="#">Settings</a></li>
                <li><i class="fa fa-bars"></i><a href="#">Subscription</a></li>
                <li><i class="fa fa-bar-chart"></i><a href="#">Auto Renewal</a> </li>
                <li><i class="fa fa-line-chart"></i><a href="#">Achievements</a></li>
                <li><i class="fa fa-bar-chart"></i><a href="#">Auto Renewal</a> </li>
                <li><i class="fa fa-line-chart"></i><a href="#">Achievements</a></li>
                <li><i class="fa fa-area-chart"></i><a href="#">Logout</a></li>
              </ul>

              <div class="sidebar-widget">
                <h4>Profile Completion</h4>
                <canvas width="150" height="80" id="chart_gauge_01" class="" style="width: 160px; height: 100px;"></canvas>
                <div class="goal-wrapper">
                  <span id="gauge-text" class="gauge-value pull-left">0</span>
                  <span class="gauge-value pull-left">%</span>
                  <span id="goal-text" class="goal-value pull-right">100%</span>
                </div>
              </div>

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
