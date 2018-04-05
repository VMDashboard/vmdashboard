<?php
require('header.php');

$ret = false; //starts false, later if set will display alert
$action = $_GET['action']; //used to determine if network action has been selected

if ($action == 'network-delete') {
  $network = $_GET['network'];
  $ret = $lv->network_undefine($network) ? 'Network removed successfully' : 'Error while removing network: '.$lv->get_last_error();
}

//need to change these to actions rather than subactions
if ($subaction) {
  $name = $_GET['name'];
  if ($subaction == 'start') {
    $ret = $lv->set_network_active($name, true) ? "Network has been started successfully" : 'Error while starting network: '.$lv->get_last_error();
  } else if ($subaction == 'stop') {
    $ret = $lv->set_network_active($name, false) ? "Network has been stopped successfully" : 'Error while stopping network: '.$lv->get_last_error();
  } else if (($subaction == 'dumpxml') || ($subaction == 'edit')) {
    $xml = $lv->network_get_xml($name, false);
    if ($subaction == 'edit') {
      if (@$_POST['xmldesc']) {
        $ret = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
          'Error changing network definition: '.$lv->get_last_error();
      } else {
        $ret = 'Editing network XML description: <br/><br/><form method="POST"><table><tr><td>Network XML description: </td>'.
          '<td><textarea name="xmldesc" rows="25" cols="90%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
          '<input type="submit" value=" Edit domain XML description "></tr></form>';
      }
    } else {
      $ret = 'XML dump of network <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_network_xml($name, false));
    }
  }
}

require('navbar.php'); //bring in the sidebar and menu
?>

<?php
//alert
if ($ret != "") {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
?>

<script>
function networkDeleteWarning(linkURL) {
  swal({
    title: 'Are you sure?',
    text: 'This will delete the network configuration',
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
          <h4 class="card-title">Private Networks</h4>
          <a href="network-wizard.php"><i class="fas fa-plus"></i> Create new network </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">

            <?php
            $tmp = $lv->get_networks(VIR_NETWORKS_ALL);
            echo "<table class='table'>" .
              "<thead class='text-primary'><tr>" .
              "<th>Network name</th>" .
              "<th>Network state</th>" .
              "<th>Gateway IP Address</th>" .
              "<th>IP Address Range</th>" .
              "<th>Forwarding</th>" .
              "<th>DHCP Range</th>" .
              "<th>Actions</th>" .
              "</tr></thead>";

            for ($i = 0; $i < sizeof($tmp); $i++) {
              $tmp2 = $lv->get_network_information($tmp[$i]);
              $ip = '';
              $ip_range = '';
              $activity = $tmp2['active'] ? 'Active' : 'Inactive';
              $dhcp = 'Disabled';
              $forward = 'None';

              if (array_key_exists('forwarding', $tmp2) && $tmp2['forwarding'] != 'None') {
                if (array_key_exists('forward_dev', $tmp2))
                  $forward = $tmp2['forwarding'].' to '.$tmp2['forward_dev'];
                else
                  $forward = $tmp2['forwarding'];
              }

              if (array_key_exists('dhcp_start', $tmp2) && array_key_exists('dhcp_end', $tmp2))
                $dhcp = $tmp2['dhcp_start'].' - '.$tmp2['dhcp_end'];

              if (array_key_exists('ip', $tmp2))
                $ip = $tmp2['ip'];

              if (array_key_exists('ip_range', $tmp2))
                $ip_range = $tmp2['ip_range'];

              $act = "<a href=\"?action={$_GET['action']}&amp;subaction=" . ($tmp2['active'] ? "stop" : "start");
              $act .= "&amp;name=" . urlencode($tmp2['name']) . "\">";
              $act .= ($tmp2['active'] ? "Stop" : "Start") . " network</a>";
              $act .= " | <a href=\"?action={$_GET['action']}&amp;subaction=dumpxml&amp;name=" . urlencode($tmp2['name']) . "\">Dump network XML</a>";

              if (!$tmp2['active']) {
                $act .= ' | <a href="?action='.$_GET['action'].'&amp;subaction=edit&amp;name='. urlencode($tmp2['name']) . '">Edit network</a>';
                $act .= " | <a onclick=\"networkDeleteWarning('?action=network-delete&amp;network=".$tmp2['name']."')\" href=\"#\">Delete</a>";
              }

              echo "<tr>" .
                "<td>{$tmp2['name']}</td>" .
                "<td>$activity</td>" .
                "<td>$ip</td>" .
                "<td>$ip_range</td>" .
                "<td>$forward</td>" .
                "<td>$dhcp</td>" .
                "<td>$act</td>" .
                "</tr>";
            }
            echo "</table>";
            ?>

          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12" style="display:none;">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"> Network Filters</h4>
          <p class="category"> Network filters for domain</p>
        </div>
        <div class="card-body">
          <div class="table-responsive">

<?php
echo "<h2>Network filters</h2>";
echo "Here you can see all the network filters defined";
$ret = false;

if (array_key_exists('subaction', $_GET)) {
  $uuid = $_GET['uuid'];
  $name = $_GET['name'];
  if ($_GET['subaction'] == 'dumpxml')
    $ret = "XML dump of nwfilter <i>$name</i>:<br/><br/>" . htmlentities($lv->get_nwfilter_xml($uuid));
}

$tmp = $lv->get_nwfilters();
echo "<table class='table'>" .
  "<tr>" .
  "<th>Name</th>" .
  "<th>UUID</th>" .
  "<th>Action</th>" .
  "</tr>\n";

for ($i = 0; $i < sizeof($tmp); $i++) {
  $name = libvirt_nwfilter_get_name($tmp[$i]);
  $uuid = libvirt_nwfilter_get_uuid_string($tmp[$i]);
  echo "<tr>" .
    "<td>" . $name . "</td>" .
    "<td>" . $uuid . "</td>" .
    "<td><a href=\"?action=$action&amp;subaction=dumpxml&amp;name=$name&amp;uuid={$uuid}\">Dump configuration</a></td>" .
    "</tr>\n";
}

echo "</table>\n";
?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
require('footer.php');
?>
