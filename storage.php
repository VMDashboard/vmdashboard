<?php
require('header.php');
require('navbar.php');
?>

<div class="panel-header panel-header-sm"></div>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Storage Pools</h4>
          <a href="storage.php"><i class="fas fa-plus"></i> New storage pool </a><br />
        </div>
        <div class="card-body">





          <?php
              if ($action == 'storage-pools') {
                $msg = '';

                if ($subaction == 'volume-delete') {
                  if ((array_key_exists('confirm', $_GET)) && ($_GET['confirm'] == 'yes')) {
                    $msg = $lv->storagevolume_delete( base64_decode($_GET['path']) ) ? 'Volume has been deleted successfully' : 'Cannot delete volume';
                  } else {
                    $msg = "<table>" .
                      "<tr>" .
                      "<td colspan=\"2\">" .
                      "<b>Do you really want to delete volume <i>".base64_decode($_GET['path'])."</i> ?</b><br/>" .
                      "</td>" .
                      "</tr>" .
                      "<tr align=\"center\">" .
                      "<td>" .
                      "<a href=". $_SERVER['REQUEST_URI'] . "&amp;confirm=yes>Yes, delete it</a>" .
                      "</td>" .
                      "<td>" .
                      "<a href=\"?action=".$action."\">No, go back</a>" .
                      "</td>" .
                      "</tr>" .
                      "</table>" ;
                  }

                } else if ($subaction == 'volume-create') {
                  if (array_key_exists('sent', $_POST)) {
                    $msg = $lv->storagevolume_create($_GET['pool'], $_POST['name'], $_POST['capacity'], $_POST['allocation'], $_POST['type']) ?
                      'Volume has been created successfully' : 'Cannot create volume';
                  } else {
                    $msg = "<h3>Create a new volume</h3><form method=\"POST\">" .
                      "<table>" .
                      "<tr>" .
                      "<td>Volume name: </td>" .
                      "<td><input type=\"text\" name=\"name\"></td>" .
                      "</tr>" .
                      "<tr>" .
                      "<td>Capacity (e.g. 10M or 1G): </td>" .
                      "<td><input type=\"text\" name=\"capacity\"></td>" .
                      "</tr>" .
                      "<tr>" .
                      "<td>Allocation (e.g. 10M or 1G): </td>" .
                      "<td><input type=\"text\" name=\"allocation\"></td>" .
                      "</tr>" .
                      "<tr>" .
                      "<td>Format Type (e.g. qcow2 or raw): </td>" .
                      "<td><input type=\"text\" name=\"type\" value=\"qcow2\"></td>" .
                      "</tr>" .
                      "<tr align=\"center\">" .
                      "<td colspan=\"2\"><input type=\"submit\" value=\" Add storage volume \"></td>" .
                      "</tr>" .
                      "<input type=\"hidden\" name=\"sent\" value=\"1\" />" .
                      "</table>" .
                      "</form>";
                  }
                }
          	     echo "<br/>";
          		echo "<br/>";
          		echo (strpos($msg, '<form')) ? $msg : '<pre>'.$msg.'</pre>';
          		echo "<br/>";
              echo "<br/>";
          	 }

?>






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
                         "<td><a href=\"?action=storage-pools&amp;path=$path&amp;subaction=volume-delete\">Delete volume</a></td>" .
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
