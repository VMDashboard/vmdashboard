<?php
require('header.php');

$ret = false; //starts false, later if set will display alert
$action = $_GET['action']; //used to determine if network action has been selected

$name = $_GET['name'];

if ($action == 'network-delete') {
  $network = $_GET['network'];
  $ret = $lv->network_undefine($network) ? 'Network removed successfully' : 'Error while removing network: '.$lv->get_last_error();
}

if ($action == 'start') {
  $ret = $lv->set_network_active($name, true) ? "Network has been started successfully" : 'Error while starting network: '.$lv->get_last_error();
}

if ($action == 'stop') {
  $ret = $lv->set_network_active($name, false) ? "Network has been stopped successfully" : 'Error while stopping network: '.$lv->get_last_error();
}

if (($action == 'dumpxml') || ($action == 'edit')) {
  $xml = $lv->network_get_xml($name, false);
  if ($action == 'edit') {
    if (@$_POST['xmldesc']) {
      $ret = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
        'Error changing network definition: '.$lv->get_last_error();
    } else {
      $network_xml = 'Editing <strong>'.$name.'</strong> network XML description: <br/><br/><form method="POST">'.
        '<textarea name="xmldesc" rows="17" cols="2" style="width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;" >'.$xml.'</textarea><br/><br/>'.
        '<input type="submit" value=" Edit domain XML description "></form><br/><br/>';
    }
  } else {
    $network_xml = 'XML dump of network <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_network_xml($name, false));
  }
}


require('navigation.php'); //bring in the sidebar and menu
?>

<?php
//alert
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
function networkDeleteWarning(linkURL) {
  swal("Delete network?", {
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
<p><a href="network-wizard.php"><i class="fa fa-plus"></i> Create new network </a></p>
<?php echo $network_xml; ?>

<!-- start project list -->
<?php
$tmp = $lv->get_networks(VIR_NETWORKS_ALL);
echo "<table class='table table-striped projects'>" .
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

  $act = "<a href=\"?action=" . ($tmp2['active'] ? "stop" : "start");
  $act .= "&amp;name=" . urlencode($tmp2['name']) . "\">";
  $act .= ($tmp2['active'] ? "Stop" : "Start") . " network</a>";
  $act .= " | <a href=\"?action=dumpxml&amp;name=" . urlencode($tmp2['name']) . "\">Dump network XML</a>";

  if (!$tmp2['active']) {
    $networkName = $tmp2['name'];
    $deleteURL = "?action=network-delete&amp;network=$networkName"
    $act .= ' | <a href="?action=edit&amp;name='. urlencode($tmp2['name']) . '">Edit network</a>';
    //$act .= " | <a onclick=\"networkDeleteWarning('?action=network-delete&amp;network=".$tmp2['name']."')\" href=\"#\">Delete</a>";
    $act .= " | <a onclick=\"networkDeleteWarning($deleteURL)\" href=\"#\">Delete</a>";
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
  </div>
</div>


<?php
require('footer.php');
?>
