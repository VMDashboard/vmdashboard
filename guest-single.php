<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);
$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];
$page = basename($_SERVER['PHP_SELF']);


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
$listfile = "tokens.list";
$list = file_put_contents($listfile, $liststring);


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
    header('Location: guests.php');
}


//Disk actions
if ($action == 'domain-disk-remove') {
  $ret = $lv->domain_disk_remove($domName, $_GET['dev']) ? 'Disk has been removed successfully' : 'Cannot remove disk: '.$lv->get_last_error();
}


//Snapshot Actions
if ($action == 'domain-snapshot-create') {
  $msg = $lv->domain_snapshot_create($domName) ? "Snapshot for $domName successfully created" : 'Error while taking snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-delete') {
  $snapshot = $_GET['snapshot'];
  $msg = $lv->domain_snapshot_delete($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully deleted" : 'Error while deleting snapshot of domain: '.$lv->get_last_error();
}

if ($action == 'domain-snapshot-revert') {
  $snapshot = $_GET['snapshot'];
  $msg = $lv->domain_snapshot_revert($domName, $snapshot) ? "Snapshot $snapshot for $domName successfully applied" : 'Error while reverting snapshot of domain: '.$lv->get_last_error();
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
  if (@$_POST['xmldesc']) {
    $ret = $lv->domain_change_xml($domName, $_POST['xmldesc']) ? "Domain definition has been changed" : 'Error changing domain definition: '.$lv->get_last_error();
  }
  header("Location: $page?uuid=$uuid");
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

require('navbar.php');
?>

<script type="text/javascript">
function retMessage() {
  swal({
    title: 'Are you sure?',
    text: 'This will remove the disk from the configuration',
    type: 'warning',
    confirmButtonText: 'Yes, remove it!',
    showCancelButton: true
  })
}
</script>

<script type="text/javascript"> retMessage(); </script>
<script type="text/javascript"> swal('Good job!','You clicked the button!','success') </script>

<div class="panel-header panel-header-sm"></div>
<div class="content">

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h2 class="title"><a href="?uuid=<?php echo $uuid; ?>"><?php echo $domName; ?></a></h2>
          <hr>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <?php
              /* General information */
              echo "<h3>General Information</h3>";
              echo "<b>Domain type: </b>".$lv->get_domain_type($domName).'<br/>';
              echo "<b>Domain emulator: </b>".$lv->get_domain_emulator($domName).'<br/>';
              echo "<b>Domain memory: </b>$mem<br/>";
              echo "<b>Number of vCPUs: </b>$cpu<br/>";
              echo "<b>Domain state: </b>$state<br/>";
              echo "<b>Domain architecture: </b>$arch<br/>";
              echo "<b>Domain ID: </b>$id<br/>";
              echo "<b>VNC Port: </b>$vnc<br/>";
              echo '<br/>';
              if ($die)
                die('</body></html');
              echo "<br />";
              ?>
            </div>

            <div class="col-md-3">
              <h3>Actions</h3>
              <?php  if ($state == "running") { ?>
                <a href="<?php echo $url; ?>:6080/vnc_lite.html?path=?token=<?php echo $uuid; ?>" target="_blank" >
                  <i class="now-ui-icons tech_tv"></i> VNC Connection<br />
                </a>
              <?php } ?>

              <?php if ($state == "shutoff") { ?>
                <a href="?action=domain-start&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="now-ui-icons media-1_button-power"></i> Power guest on<br />
                </a>
              <?php } ?>

              <?php  if ($state == "running") { ?>
                <a href="?action=domain-stop&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                <i class="now-ui-icons media-1_button-power"></i> Power guest off<br />
                </a>
                <a href="?action=domain-pause&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="now-ui-icons media-1_button-pause"></i> Pause guest<br />
                </a>
              <?php } ?>

              <?php  if ($state == "paused") { ?>
                <a href="?action=domain-resume&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="now-ui-icons media-1_button-play"></i> Resume guest<br />
                </a>
              <?php } ?>

              <?php  if ($state != "shutoff") { ?>
                <a href="?action=domain-destroy&amp;uuid=<?php echo $uuid; ?>" target="_self" >
                  <i class="fas fa-plug"></i> Turn off<br />
                </a>
              <?php } ?>

              <?php  if ($state == "shutoff") { ?>
                <a onclick="domainDeleteWarning('?action=domain-delete&amp;uuid=<?php echo $_GET['uuid'] ?>')" href="#">
                  <i class="fas fa-trash"></i> Delete guest<br />
                </a>
              <?php } ?>

            </div>

            <div class="col-md-4">
              <?php
              if ($state == "running") {
                //screenshot will get raw png data at 300 pixels wide
                $screenshot = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], 400);
                //the raw png data needs to be encoded to use with html img tag
                $screen64 = base64_encode($screenshot['data']);
                ?>
                <a href="<?php echo $url; ?>:6080/vnc_lite.html?path=?token=<?php echo $uuid ?>" target="_blank">
                <img src="data:image/png;base64,<?php echo $screen64 ?>" width="400px"/>
                </a>
                <?php
              } else if ($state == "paused") {
                echo "<img src='assets/img/paused.png' width='400px' >";
              } else {
                echo "<img src='assets/img/shutdown.png' width='400px' >";
              }
              ?>
            </div>





          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <?php
          /* Disk information */
          echo "<h3>Storage</h3>";
          echo "<a title='Add new disk' href=guest-disk-wizard.php?action=domain-disk-add&amp;uuid=" . $uuid . "><i class='fas fa-database'></i> Add new disk</a><br />";
          $tmp = $lv->get_disk_stats($domName);
          if (!empty($tmp)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              //"<thead class='text-primary'>" .
              "<tr>" .
              "<th>Disk storage</th>" .
              "<th>Storage driver type</th>" .
              "<th>Domain device</th>" .
              "<th>Disk capacity</th>" .
              "<th>Disk allocation</th>" .
              "<th>Physical disk size</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              //"</thead>" .
              "<tbody>";
            for ($i = 0; $i < sizeof($tmp); $i++) {
              $capacity = $lv->format_size($tmp[$i]['capacity'], 2);
              $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
              $physical = $lv->format_size($tmp[$i]['physical'], 2);
              $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];
              echo "<tr>" .
                "<td>".basename($dev)."</td>" .
                "<td>{$tmp[$i]['type']}</td>" .
                "<td>{$tmp[$i]['device']}</td>" .
                "<td>$capacity</td>" .
                "<td>$allocation</td>" .
                "<td>$physical</td>" .
                "<td>" .
                  "<a title='Remove disk device' onclick=\"diskRemoveWarning('?action=domain-disk-remove&amp;dev=" . $tmp[$i]['device'] . "&amp;uuid=" . $_GET['uuid'] . "')\" href='#'><i class='fas fa-trash-alt'></i></a>" .
                "</td>" .
                "</tr>";
            }
            echo "</tbody></table></div>";
          } else {
            echo "Domain doesn't have any disk devices";
          }
          ?>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <?php
          /* Network interface information */
          echo "<h3>Network devices</h3>";
          $tmp = $lv->get_nic_info($domName);
          if (!empty($tmp)) {
            $anets = $lv->get_networks(VIR_NETWORKS_ACTIVE);
            echo "<table>" .
              "<tr>" .
              "<th>MAC Address</th>" .
              "<th>NIC Type</th>" .
              "<th>Network</th>" .
              "<th>Network active</th>" .
              "<th>Actions</th>" .
              "</tr>";
            for ($i = 0; $i < sizeof($tmp); $i++) {
              if (in_array($tmp[$i]['network'], $anets))
                $netUp = 'Yes';
              else
                $netUp = 'No <a href="">[Start]</a>';
              echo "<tr>" .
                "<td>{$tmp[$i]['mac']}</td>" .
                "<td align=\"center\">{$tmp[$i]['nic_type']}</td>" .
                "<td align=\"center\">{$tmp[$i]['network']}</td>" .
                "<td align=\"center\">$netUp$spaces</td>" .
                "<td align=\"center\">" .
                  "<a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-remove&amp;mac={$tmp[$i]['mac']}\">" .
                  "Remove network card</a>" .
                "</td>" .
                "</tr>";
            }
            echo "</table>";
            echo "<br/><a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-add\">Add new network card</a>";
          } else {
            echo '<p>Domain doesn\'t have any network devices</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <?php
          /* Snapshot information */
          echo "<h3>Snapshots</h3>";
          echo "<a title='Create snapshot' href=?action=domain-snapshot-create&amp;uuid=" . $_GET['uuid'] . "><i class='now-ui-icons media-1_camera-compact'></i> Create new snapshot</a><br />";
          $tmp = $lv->list_domain_snapshots($dom);
          if (!empty($tmp)) {
            echo "<div class='table-responsive'>" .
              "<table class='table'>" .
              //"<thead class='text-primary'>" .
              "<tr>" .
              "<th>Name</th>" .
              "<th>Creation Time</th>" .
              "<th>State</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              //"</thead>" .
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
                <a title='Delete snapshot' onclick=\"snapshotDeleteWarning('?action=domain-snapshot-delete&amp;snapshot=" . $value . "&amp;uuid=" . $_GET['uuid'] . "')\" href='#'><i class='fas fa-trash-alt'></i></a>
                <a title='Revert snapshot' href=?action=domain-snapshot-revert&amp;uuid=" . $_GET['uuid'] . "&amp;snapshot=" . $value . "><i class='fas fa-exchange-alt'></i></a>
                <a title='Snapshot XML' href=?action=domain-snapshot-xml&amp;uuid=" . $_GET['uuid'] . "&amp;snapshot=" . $value . "><i class='fas fa-code'></i></a>
                </td>";
              echo "</tr>";
            }
            echo "</tbody></table></div>";
          } else {
            echo "Domain does not have any snapshots";
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


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h3>XML Information (Advanced)</h3>
          <div class="row">
            <div class="col-lg-4 col-md-8">
            <!--
              color-classes: "nav-pills-primary", "nav-pills-info", "nav-pills-success", "nav-pills-warning","nav-pills-danger"
            -->
              <ul class="nav nav-pills nav-pills-primary nav-pills-icons flex-column" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#linkxml" role="tablist">
                    <i class="now-ui-icons text_caps-small"></i>
                    Info
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#linkxmlview" role="tablist">
                    <i class="now-ui-icons education_paper"></i>
                    View XML
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#linkxmledit" role="tablist">
                    <i class="now-ui-icons ui-2_settings-90"></i>
                    Edit XML
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-md-8">
              <div class="tab-content">
                <div class="tab-pane active" id="linkxml">
                  The virtual machine guest information is stored in XML format. Viewing the XML information will display all configured settings. Editing the XML can be performed when a guest is shutdown.
                </div>
                <div class="tab-pane" id="linkxmlview">
                  <?php
                  /* XML information */
                  $inactive = (!$lv->domain_is_running($domName)) ? true : false;
                  $xml = $lv->domain_get_xml($domName, $inactive);
                  $ret = htmlentities($xml);
                  echo "<textarea rows=\"17\" cols=\"2\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0;\" readonly>" . $ret . "</textarea>";
                  ?>
                </div>
                <div class="tab-pane" id="linkxmledit">
                  <?php
                  if ($state == "shutoff"){
                    $ret = "<form method=\"POST\" action=?action=domain-edit&amp;uuid=" . $_GET['uuid'] . " >" .
                      "<textarea name=\"xmldesc\" rows=\"17\" cols=\"2\" style=\"width: 100%; margin: 0; padding: 0; border-width: 0;\" >" . $xml . "</textarea>" .
                      "<br /> <br /> <input type=\"submit\" value=\"Save XML\"></form>";
                    echo $ret;
                  } else {
                    echo "Virtual machine must be shutdown before editing XML";
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
require('footer.php');
?>

<script>
function domainDeleteWarning(linkURL) {
  swal({
    title: 'Are you sure?',
    text: 'This will delete the virtual machine configuration',
    type: 'warning',
    confirmButtonText: 'Yes, delete it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}

function diskRemoveWarning(linkURL) {
  swal({
    title: 'Are you sure?',
    text: 'This will remove the disk from the configuration',
    type: 'warning',
    confirmButtonText: 'Yes, remove it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}

function snapshotDeleteWarning(linkURL) {
  swal({
    title: 'Are you sure?',
    text: 'This will delete the snapshot',
    type: 'warning',
    confirmButtonText: 'Yes, delete it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}
</script>
