<?php
require('header.php');
require('navbar.php');
?>

<div class="panel-header panel-header-sm">
</div>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"> Storage Pools</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead class=" text-primary">
                <th>Name</th>
                <th>Activity</th>
                <th>Volume Count</th>
                <th>State</th>
                <th>Capacity</th>
                <th>Allocation</th>
                <th>Available</th>
                <th>Path</th>
                <th>Permissions</th>
                <th>Ownership</th>
                <th>Actions</th>
              </thead>
              <tbody>

<?php
$pools = $lv->get_storagepools();
for ($i = 0; $i < sizeof($pools); $i++) {
  $info = $lv->get_storagepool_info($pools[$i]);
  $act = $info['active'] ? 'Active' : 'Inactive';
  echo "<tr align=\"center\">" .
    "<td>{$pools[$i]}</td>" .
    "<td>$act</td>" .
    "<td>{$info['volume_count']}</td>" .
    "<td>{$lv->translate_storagepool_state($info['state'])}</td>" .
    "<td>{$lv->format_size($info['capacity'], 2)}</td>" .
    "<td>{$lv->format_size($info['allocation'], 2)}</td>" .
    "<td>{$lv->format_size($info['available'], 2)}</td>" .
    "<td>{$info['path']}</td>" .
    "<td>{$lv->translate_perms($info['permissions'])}</td>" .
    "<td>{$info['id_user']} / {$info['id_group']}</td>" .
    "<td><a href=\"storage-volume-wizard.php?action=storage-pools&amp;pool={$pools[$i]}&amp;subaction=volume-create\">Create new volume</a></td>" .
    "</tr>";

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

            if ($info['volume_count'] > 0) {
                echo "<tr>" .
                     "<td colspan=\"10\" style='padding-left: 40px'><table>" .
                     "<tr>" .
                     "<th>Name</th>" .
                     "<th>Type</th>" .
                     "<th>Capacity</th>" .
                     "<th>Allocation</th>" .
                     "<th>Path</th>" .
                     "<th>Actions</th>" .
                     "</tr>";
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
                echo "</table></td>" .
                     "</tr>";
            }
        }

		?>




                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php
require('footer.php');
?>
