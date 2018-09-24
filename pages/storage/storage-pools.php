<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  $_SESSION['return_location'] = $_SERVER['PHP_SELF']; //sets the return location used on login page
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

if ($action == 'volume-delete') {
  $notification = $lv->storagevolume_delete( base64_decode($_SESSION['path']) ) ? "" : 'Cannot delete volume';
}

if ($action == 'volume-clone') {
  $pool_res = $lv->get_storagepool_res($pool);
  $notification = $lv->storagevolume_create_xml_from($pool_res, $path, $filename) ? "" : 'Cannot clone volume: '.$lv->get_last_error();
}

if ($action == 'pool-delete') {
  $res = $lv->get_storagepool_res($pool);
  $notification = $lv->storagepool_undefine($res) ? "" : 'Cannot remove pool';
}

if ($action == 'pool-destroy') {
  $res = $lv->get_storagepool_res($pool);
  $notification = $lv->storagepool_destroy($res) ? "" : 'Cannot stop pool';
}

if ($action == 'pool-start') {
  $res = $lv->get_storagepool_res($pool);
  $notification = $lv->storagepool_create($res) ? "" : 'Cannot start pool';
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

?>

<script>
function volumeDeleteWarning(linkURL, fileName) {
    var r = confirm("Deleting volume " + fileName + ".");
    if (r == true) {
      window.location = linkURL;
    }
}
</script>

<div class="content">
  <p style="text-align:right; padding-right:20px;"><a  href="storage-add-pool.php"><i class="fa fa-plus"></i> Add storage pool </a> </p>

  <?php
  $pools = $lv->get_storagepools();

  if (empty($pools)) {
    ?>
      <div class="col-12">
        <div class="card card-stats-left">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">storage</i>
            </div>
            <h3 class="card-title">Storage volumes </h3>
            <p class="card-category"></p>
          </div>
          <div class="card-body">
          <p>There are no configured storage pools. Please <a href="storage-add-pool.php">create</a> a new storage pool, then start it.</p>
          <p>Storage pools are used to contain the storage volumes (disk drives) and ISO files of your virtual machines</p>
          <br />
          <a href="storage-add-pool.php"><i class="fa fa-plus"></i> Add storage pool </a> <br /> <br />
        </div>
      </div>
    </div>
    <?php
  } else {
    for ($i = 0; $i < sizeof($pools); $i++) {
      //get the pool resource to use with refreshing the pool data
      $res = $lv->get_storagepool_res($pools[$i]);
      //refreshing the data before displaying because ISOs were not refreshing automatically and also the Available data was not correct after adding volumes
      $msg = $lv->storagepool_refresh($res) ? "Pool has been refreshed" : "Error refreshing pool: ".$lv->get_last_error();
      //getting the pool information to display the data in a table
      $info = $lv->get_storagepool_info($pools[$i]);
      $poolName = $pools[$i];
      $act = $info['active'] ? 'Active' : 'Inactive';
      ?>



    <div class="col-12">
      <div class="card card-stats-left">
        <div class="card-header card-header-warning card-header-icon">
          <div class="card-icon">
            <i class="material-icons">storage</i>
          </div>
          <h3 class="card-title">Storage Pool: <?php echo $pools[$i]; ?> </h3>
          <p class="card-category">
            <?php
            echo "<strong>State:</strong> " . $lv->translate_storagepool_state($info['state']);
            echo "&ensp; | &ensp;<strong>Capacity:</strong> " . $lv->format_size($info['capacity'], 2) ;
            echo "&ensp; | &ensp;<strong>Allocation:</strong> " . $lv->format_size($info['allocation'], 2) ;
            echo "&ensp; | &ensp;<strong>Available:</strong> " . $lv->format_size($info['available'], 2) ;
            echo "&ensp; | &ensp;<strong>Path:</strong> " . $info['path'] ;
            echo "&ensp; | &ensp;<strong>Actions:</strong> ";
            if ($lv->translate_storagepool_state($info['state']) == "Running") {
              echo "<a style=\"color:#fa8a05;\" href=\"?action=pool-destroy&amp;pool=$pools[$i]\"> Stop</a>";
            }
            if ($lv->translate_storagepool_state($info['state']) != "Running") {
              echo "<a style=\"color:#fa8a05;\" href=\"?action=pool-start&amp;pool=$pools[$i]\"> Start</a>";
              //echo "<a href=\"?action=pool-delete&amp;pool=$pools[$i]\"> | Remove</a>";
              echo "<a style=\"color:#fa8a05;\" href=\"?action=pool-delete&amp;pool=$poolName\"> | Remove</a>";
            }


            ?>
          </p>
        </div>
        <div class="card-body">
          <?php
          if ($act == "Active")
          echo "<a href=\"storage-add-volume.php?pool=$pools[$i]\"><i class=\"fa fa-plus\"></i> Create new volume </a>";
          ?>

          <br /><br />
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="text-none">
                <tr>
                  <th>File Name</th>
                  <th>Type</th>
                  <th>Capacity</th>
                  <th>Allocation</th>
                  <th>Path</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                //run code only if there are drives in the storage pool
                if ($info['volume_count'] > 0) {
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
                      "<td><a href=\"?action=volume-clone&amp;path=$path&amp;pool=$poolName&amp;filename=$filename\">Clone</a>
                      &nbsp;|&nbsp; <a onclick=\"volumeDeleteWarning('?action=volume-delete&amp;path=$path', '$filename')\" href=\"#\">Delete</a></td>" .
                      //"<td><a href=\"?action=volume-delete&amp;path=$path\" >Delete</a></td>" .
                      "</tr>";
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div> <!-- end card body -->
      </div> <!-- end card -->
    </div> <!-- end col -->


      <?php } //ends the for loop for each storage pool
    } ?>
</div> <!-- end Content -->

<script>
window.onload =  function() {
  <?php
  if ($notification) {
    echo "showNotification(\"top\",\"right\",\"$notification\");";
  }
  ?>
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
