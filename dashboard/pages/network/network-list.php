<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
  session_start();
}

// If there is no username, then we need to send them to the login
//if (!isset($_SESSION['username'])){
//  header('Location: login.php');
//}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_GET['action'])) {
  $_SESSION['action'] = $_GET['action'];
  $_SESSION['name'] = $_GET['name'];
  $_SESSION['network'] = $GET['network'];
  $_SESSION['xmldesc'] = $_POST['xmldesc'];
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');
require('../navbar.php');

$action = $_SESSION['action']; //grab the $action variable from $_SESSION
$name = $_SESSION['name'];
$network = $_SESSION['network'];
unset($_SESSION['action']); //Unset the Action Variable to prevent repeats of action on page reload
unset($_SESSION['name']); //Unset the name in case of page reload
unset($_SESSION['network']); //Unset the name in case of page reload

if ($action == 'network-delete') {
  $ret = $lv->network_undefine($network) ? 'Network removed successfully' : 'Error while removing network: '.$lv->get_last_error();
}

if ($action == 'start') {
  $ret = $lv->set_network_active($name, true) ? "Network has been started successfully" : 'Error while starting network: '.$lv->get_last_error();
}

if ($action == 'stop') {
  $ret = $lv->set_network_active($name, false) ? "Network has been stopped successfully" : 'Error while stopping network: '.$lv->get_last_error();
}

if ($action == 'edit') {
  $xml = $lv->network_get_xml($name, false);
  if (@$_SESSION['xmldesc']) {
    $ret = $lv->network_change_xml($name, $_SESSION['xmldesc']) ? "Network definition has been changed" :
      'Error changing network definition: '.$lv->get_last_error();
  } else {
    $network_xml = 'Editing <strong>'.$name.'</strong> network XML description: <br/><br/><form method="POST">'.
      '<textarea name="xmldesc" rows="17" cols="2" style="width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;" >'.$xml.'</textarea><br/><br/>'.
      '<input type="submit" value=" Edit domain XML description "></form><br/><br/>';
  }
}

if ($action == 'dumpxml') {
  $xml = $lv->network_get_xml($name, false);
  $network_xml = 'XML dump of network <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_network_xml($name, false));
}

unset($_SESSION['xmldesc']);

//Will display a sweet alert if a return message exists
if ($ret != "") {
echo "
<script>
var alert_msg = '$ret'
swal(alert_msg);
</script>";
}

?>


<div class="content">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"> Virtual Networks</h4>
    </div>
    <div class="card-body">
      <?php echo $network_xml; ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead class="text-primary">
            <th>Network name</th>
            <th>Network state</th>
            <th>Gateway IP Address</th>
            <th>IP Address Range</th>
            <th>Forwarding</th>
            <th>DHCP Range</th>
            <th>Actions</th>
          </thead>
          <tbody>
        <!-- start project list -->
        <?php
        $tmp = $lv->get_networks(VIR_NETWORKS_ALL);

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
            $deleteURL = "?action=network-delete&amp;network=$networkName";
            $currentURL = $_SERVER['PHP_SELF'];
            $act .= ' | <a href="?action=edit&amp;name='. urlencode($tmp2['name']) . '">Edit network</a>';
            //$act .= " | <a onclick=\"networkDeleteWarning('?action=network-delete&amp;network=".$tmp2['name']."')\" href=\"#\">Delete</a>";
            //$act .= " | <a onclick=\"networkDeleteWarning('$deleteURL','$currentURL')\" href=\"#\">Delete</a>";
            $act .= " | <a href=\"?action=network-delete&amp;network=$networkName\">Delete</a>";
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
        echo "</tbody></table>";
        ?>
      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
