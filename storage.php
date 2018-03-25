<?php
require('header.php');
require('navbar.php');
?>


<?php
if ($action == 'volume-delete') {
  $msg = '';
  $msg = $lv->storagevolume_delete( base64_decode($_GET['path']) ) ? 'Volume has been deleted successfully' : 'Cannot delete volume';
}
if ($msg != "") {
?>
<script>
var alertRet = "<?php echo $msg; ?>";
swal(alertRet);
</script>
<?php
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



<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Storage Pools</h4>
        </div>
        <div class="card-body">




          <?php
          $pools = $lv->get_storagepools();
          for ($i = 0; $i < sizeof($pools); $i++) {
            $info = $lv->get_storagepool_info($pools[$i]);
          ?>
          <hr>
          <div class="row">
            <div class="col-md-3">
            <font style="font-size:1.45em;line-height:2.5"><strong><?php echo $pools[$i]; ?></strong></font><br />
            <a href="storage-volume-wizard.php?action=storage-pools&amp;pool=<?php echo $pools[$i]; ?>&amp;subaction=volume-create"><i class="fas fa-plus"></i> Create new volume </a>
            <br /><br/>
          <?php $act = $info['active'] ? 'Active' : 'Inactive';
          echo "<strong>Pool Name:</strong> " . $pools[$i] . "<br />";
          echo "<strong>Activity:</strong> " . $act . "<br />";
          echo "<strong>State:</strong> " . $lv->translate_storagepool_state($info['state']) . "<br />";
          echo "<strong>Capacity:</strong> " . $lv->format_size($info['capacity'], 2) . "<br />";
          echo "<strong>Allocation:</strong> " . $lv->format_size($info['allocation'], 2) . "<br />";
          echo "<strong>Available:</strong> " . $lv->format_size($info['available'], 2) . "<br />";
          echo "<strong>Path:</strong> " . $info['path'] . "<br />";
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
                    $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
                    echo "<tr>" .
                         "<td>{$tmp_keys[$ii]}</td>" .
                         "<td>{$lv->translate_volume_type($tmp[$tmp_keys[$ii]]['type'])}</td>" .
                         "<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['capacity'], 2)}</td>" .
                         "<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['allocation'], 2)}</td>" .
                         "<td>{$tmp[$tmp_keys[$ii]]['path']}</td>" .
                         "<td><a onclick=\"volumeDeleteWarning('?action=volume-delete&amp;path=$path')\" href=\"#\">Delete volume</a></td>" .
                         "</tr>";
                }
                echo "</tbody></table></div>";
            }
        }
		?>
                  



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
