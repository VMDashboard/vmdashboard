<?php
require('header.php');
require('navigation.php');

$action = $_GET['action'];

if ($action == 'volume-delete') {
  $msg = '';
  $msg = $lv->storagevolume_delete( base64_decode($_GET['path']) ) ? 'Volume has been deleted successfully' : 'Cannot delete volume';
}

if ($action == 'pool-delete') {
  $pool = $_GET['pool'];
  $res = $lv->get_storagepool_res($pool);
  $msg = '';
  $msg = $lv->storagepool_undefine($res) ? 'Pool has been removed successfully' : 'Cannot remove pool';
}

if ($action == 'pool-destroy') {
  $pool = $_GET['pool'];
  $res = $lv->get_storagepool_res($pool);
  $msg = '';
  $msg = $lv->storagepool_destroy($res) ? 'Pool has been stopped successfully' : 'Cannot stop pool';
}

if ($action == 'pool-start') {
  $pool = $_GET['pool'];
  $res = $lv->get_storagepool_res($pool);
  $msg = '';
  $msg = $lv->storagepool_create($res) ? 'Pool has been started successfully' : 'Cannot start pool';
}

//pool-xml not yet configured
if ($action == "pool-xml") {
  $poolname = "default";
  $info = $lv->get_storagepool_info($poolname);
  echo "<textarea>";
  echo $info['xml'];
  echo "</textarea>";
}


//Check to see if the iso_uploads directory exists. If it does and ISO has been uploaded
if (file_exists('../uploads/iso_uploads')) {

  $upload_path = realpath('../uploads/iso_uploads'); //determine the actual filepath of iso_uploads on the server
  $filename = '../uploads/iso_uploads/*.iso'; //set filepath to use with glob to determine any filename that ends with .iso

  $directory = "../uploads/iso_uploads/";
  $files = glob($directory . "*.iso"); //check to see if any files with .iso exist
  if ($files) {

    $pools = $lv->get_storagepools();
    for ($i = 0; $i < sizeof($pools); $i++) {
      $info = $lv->get_storagepool_info($pools[$i]);
      if ($upload_path == $info['path']) {
        $iso_pool_exists = true;
      }
    }

    if (!$iso_pool_exists) {
      $xml = "
        <pool type='dir'>
          <name>iso_uploads</name>
          <target>
            <path>$upload_path</path>
            <permissions>
            </permissions>
          </target>
        </pool>";

      $ret = $lv->storagepool_define_xml($xml) ? "success" : "Cannot add storagepool: ".$lv->get_last_error();
    }
  }
}


?>

<script>
function volumeDeleteWarning(linkURL) {
  swal({
    title: 'Are you sure?',
    text: 'This will delete the storage volume',
    type: 'warning',
    confirmButtonText: 'Yes, delete it!',
    showCancelButton: true
  }).then(function($result) {
    // Redirect the user
    window.location = linkURL;
  });
}
</script>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Domain List</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Guests</h2>
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
          <div class="x_content">



          <?php
          $pools = $lv->get_storagepools();
          for ($i = 0; $i < sizeof($pools); $i++) {
            $info = $lv->get_storagepool_info($pools[$i]);
            ?>
            <hr>
            <div class="row">
              <div class="col-md-3">
                <font style="font-size:1.45em;line-height:2.5"><strong><?php echo $pools[$i]; ?></strong></font><br />
                <?php $act = $info['active'] ? 'Active' : 'Inactive';
                if ($act == "Active")
                  echo "<a href=\"storage-wizard-volumes.php?action=storage-pools&amp;pool=$pools[$i]&amp;subaction=volume-create\"><i class=\"fa fa-plus\"></i> Create new volume </a> <br/> <br />";
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
                  echo "<a href=\"?action=pool-delete&amp;pool=$pools[$i]\"> | Remove</a>";
                }
            ?>
              </div>


              <div class="col-md-9">
                <?php
                //sub table
                if ($info['volume_count'] > 0) {
                  echo "<div class=\"table-responsive\">" .
                    "<table class=\"table\">" .
                    "<thead>" .
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
                    $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
                    echo "<tr>" .
                      "<td>{$tmp_keys[$ii]}</td>" .
                      "<td>{$lv->translate_volume_type($tmp[$tmp_keys[$ii]]['type'])}</td>" .
                      "<td>$capacity</td>" .
                      "<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['allocation'], 2)}</td>" .
                     "<td>{$tmp[$tmp_keys[$ii]]['path']}</td>" .
                     "<td><a onclick=\"volumeDeleteWarning('?action=volume-delete&amp;path=$path')\" href=\"#\">Delete volume</a></td>" .
                     "</tr>";
                   }
                   echo "</tbody></table></div>";
                 }

		             ?>

              </div>
            </div>

          <?php } //ends the for loop for each storage pool ?>

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
