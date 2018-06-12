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
if (isset($_GET['action'])) {
    $_SESSION['action'] = $_GET['action'];
    $_SESSION['path'] = $_GET['path'];
    $_SESSION['pool'] = $_GET['pool'];
    $_SESSION['filename'] = $_GET['filename'];
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
require('../header.php');
require('../navbar.php');

$action = $_SESSION['action']; //grab the $action variable from $_SESSION
$pool = $_SESSION['pool'];
$path = base64_decode($_SESSION['path']); //path was encoded for passing through URL
$filename = $_SESSION['filename'];
$ret = "";


if ($action == 'volume-delete') {
  $ret = $lv->storagevolume_delete( base64_decode($_SESSION['path']) ) ? 'Volume has been deleted successfully' : 'Cannot delete volume';
}

if ($action == 'volume-clone') {
  $pool_res = $lv->get_storagepool_res($pool);
  $ret = $lv->storagevolume_create_xml_from($pool_res, $path, $filename) ? 'Volume has been cloned successfully' : 'Cannot clone volume: '.$lv->get_last_error();
}

if ($action == 'pool-delete') {
  $res = $lv->get_storagepool_res($pool);
  $ret = $lv->storagepool_undefine($res) ? 'Pool has been removed successfully' : 'Cannot remove pool';
}

if ($action == 'pool-destroy') {
  $res = $lv->get_storagepool_res($pool);
  $ret = $lv->storagepool_destroy($res) ? 'Pool has been stopped successfully' : 'Cannot stop pool';
}

if ($action == 'pool-start') {
  $res = $lv->get_storagepool_res($pool);
  $ret = $lv->storagepool_create($res) ? 'Pool has been started successfully' : 'Cannot start pool';
}

//pool-xml not yet configured
if ($action == "pool-xml") {
  $poolname = "default";
  $info = $lv->get_storagepool_info($poolname);
  echo "<textarea>";
  echo $info['xml'];
  echo "</textarea>";
}

unset($_SESSION['action']); //Unset the Action Variable to prevent repeats of action on page reload
unset($_SESSION['path']);
unset($_SESSION['pool']);
unset($_SESSION['filename']);

//Will display a sweet alert if a return message exists
if ($ret != "") {
echo "
<script>
var alert_msg = '$ret'
swal(alert_msg);
</script>";
}

?>

<script>
function volumeDeleteWarning(linkURL, fileName) {
  swal({
    title: 'Are you sure?',
    text: 'This will delete ' + fileName,
    type: 'warning',
    confirmButtonText: 'Yes, delete it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}

function poolDeleteWarning(linkURL, poolName) {
  swal({
    title: 'Are you sure?',
    text: 'This will remove ' + poolName,
    type: 'warning',
    confirmButtonText: 'Yes, remove it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}
</script>


<div class="content">
  <div class="card">
    <?php
    $pools = $lv->get_storagepools();
    for ($i = 0; $i < sizeof($pools); $i++) {
      //get the pool resource to use with refreshing the pool data
      $res = $lv->get_storagepool_res($pools[$i]);
      //refreshing the data before displaying because ISOs were not refreshing automatically and also the Available data was not correct after adding volumes
      $msg = $lv->storagepool_refresh($res) ? "Pool has been refreshed" : "Error refreshing pool: ".$lv->get_last_error();
      //getting the pool information to display the data in a table
      $info = $lv->get_storagepool_info($pools[$i]);
      $poolName = $pools[$i];
      ?>
    <div class="row">
      <div class="col-md-3">
        <div class="card-header">
          <h5 class="card-title">Pool: <?php echo $pools[$i]; ?> </h5>
        </div>
        <div class="card-body">
          <?php $act = $info['active'] ? 'Active' : 'Inactive';
          if ($act == "Active")
          echo "<a href=\"storage-add-volume.php?pool=$pools[$i]\"><i class=\"fa fa-plus\"></i> Create new volume </a> <br/> <br />";
          echo "<strong>Pool Name:</strong> " . $pools[$i] . "<br />";
          echo "<strong>Activity:</strong> " . $act . "<br />";
          echo "<strong>State:</strong> " . $lv->translate_storagepool_state($info['state']) . "<br />";
          echo "<strong>Capacity:</strong> " . $lv->format_size($info['capacity'], 2) . "<br />";
          echo "<strong>Allocation:</strong> " . $lv->format_size($info['allocation'], 2) . "<br />";
          echo "<strong>Available:</strong> " . $lv->format_size($info['available'], 2) . "<br />";
          echo "<strong>Path:</strong> " . $info['path'] . "<br />";
          echo "<strong>Actions:</strong> ";
          if ($lv->translate_storagepool_state($info['state']) == "Running") {
            echo "<a href=\"?action=pool-destroy&amp;pool=$pools[$i]\">Stop</a>";
          }
          if ($lv->translate_storagepool_state($info['state']) != "Running") {
            echo "<a href=\"?action=pool-start&amp;pool=$pools[$i]\">Start</a>";
            //echo "<a href=\"?action=pool-delete&amp;pool=$pools[$i]\"> | Remove</a>";
            echo "<a onclick=\"poolDeleteWarning('?action=pool-delete&amp;pool=$poolName', '$poolName')\" href=\"#\"> | Remove</a>";
          }
          ?>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card-body">
          <?php
          //sub table
          if ($info['volume_count'] > 0) {
            echo "<div class=\"table-responsive\">" .
              "<table class=\"table table-striped\">" .
              "<thead class=\"text-primary\">" .
              "<tr>" .
              "<th>File Name</th>" .
              "<th>Type</th>" .
              "<th>Capacity</th>" .
              "<th>Allocation</th>" .
              "<th>Path</th>" .
              "<th>Actions</th>" .
              "</tr>" .
              "</thead>" .
              "</tbody>";
            $tmp = $lv->storagepool_get_volume_information($pools[$i]);
            $tmp_keys = array_keys($tmp);
            for ($ii = 0; $ii < sizeof($tmp); $ii++) {
              $capacity = $lv->format_size($tmp[$tmp_keys[$ii]]['capacity'], 2);
                if ($capacity == 0)
                  continue; //used to not display directories
              $filename = $tmp_keys[$ii];
              $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
              echo "<tr>" .
                "<td>$filename</td>" .
                "<td>{$lv->translate_volume_type($tmp[$tmp_keys[$ii]]['type'])}</td>" .
                "<td>$capacity</td>" .
                "<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['allocation'], 2)}</td>" .
               "<td>{$tmp[$tmp_keys[$ii]]['path']}</td>" .
               "<td><a onclick=\"volumeDeleteWarning('?action=volume-delete&amp;path=$path', '$filename')\" href=\"#\">Delete</a>
                  <a href=\"?action=volume-clone&amp;path=$path&amp;pool=$poolName&amp;filename=$filename\">&nbsp;| Clone</a></td>" .
               //"<td><a href=\"?action=volume-delete&amp;path=$path\" >Delete</a></td>" .
               "</tr>";
             }
             echo "</tbody></table></div>";
           }
           ?>
        </div>
      </div>

    </div> <!-- end row -->
    <hr>
    <?php } //ends the for loop for each storage pool ?>
  </div>
</div>


<?php
require('../footer.php');
?>
