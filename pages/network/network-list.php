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
  $_SESSION['name'] = $_GET['name'];
  $_SESSION['network'] = $_GET['network'];
  $_SESSION['xmldesc'] = $_POST['xmldesc'];
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

$action = $_SESSION['action']; //grab the $action variable from $_SESSION
$name = $_SESSION['name']; //will be the name of the network to perform actions on
$network = $_SESSION['network']; //used for delete confirmation
$xmldesc = $_SESSION['xmldesc']; //used to hold changed XML data
unset($_SESSION['action']); //Unset the Action Variable to prevent repeats of action on page reload
unset($_SESSION['name']); //Unset the name in case of page reload
unset($_SESSION['network']); //Unset the name in case of page reload
unset($_SESSION['xmldesc']);

if ($action == 'network-delete') {
  $notification = $lv->network_undefine($network) ? "" : 'Error while removing network: '.$lv->get_last_error();
}

if ($action == 'start') {
  $notification = $lv->set_network_active($name, true) ? "" : 'Error while starting network: '.$lv->get_last_error();
}

if ($action == 'stop') {
  $notification = $lv->set_network_active($name, false) ? "" : 'Error while stopping network: '.$lv->get_last_error();
}

if ($action == 'edit') {
  $xml = $lv->network_get_xml($name, false);
  if ($xmldesc != "") {
    $notification = $lv->network_change_xml($name, $xmldesc) ? "" : 'Error changing network definition: '.$lv->get_last_error();
  } else {
    $network_xml = 'Editing <strong>'.$name.'</strong> network XML description: <br/><br/><form action="?name='.$name.'&action=edit" method="POST">'.
      '<textarea name="xmldesc" rows="17" cols="2" style="width: 100%; margin: 0; padding: 0; border-width: 0; background-color:#ebecf1;" >'.$xml.'</textarea><br/><br/>'.
      '<input type="submit" value=" Save "></form><br/><br/>';
  }
}

if ($action == 'dumpxml') {
  $xml = $lv->network_get_xml($name, false);
  $network_xml = 'XML dump of network <i>'.$name.'</i>:<br/><br/><pre>' . htmlentities($lv->get_network_xml($name, false)) . '</pre>';

}



require('../navbar.php');

?>

<div class="content">

  <div class="card card-stats-left">
    <div class="card-header card-header-warning card-header-icon">
      <div class="card-icon">
        <i class="material-icons">device_hub</i>
      </div>
      <h3 class="card-title"> Virtual Networks</h3>
      <p class="card-category"></p>
    </div>
    <div class="card-body">

      <a href="network-add-lan.php"><i class="fa fa-plus"></i> Create new network</a> <br /> <br />

      <?php echo $network_xml; ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="text-none">
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
          $act .= ($tmp2['active'] ? "Disable " : "Enable ") . "</a>";
          $act .= " | <a href=\"?action=dumpxml&amp;name=" . urlencode($tmp2['name']) . "\"> XML </a>";

          if (!$tmp2['active']) {
            $networkName = $tmp2['name'];
            $deleteURL = "?action=network-delete&amp;network=$networkName";
            $currentURL = $_SERVER['PHP_SELF'];
            $act .= ' | <a href="?action=edit&amp;name='. urlencode($tmp2['name']) . '"> Edit </a>';
            //$act .= " | <a onclick=\"networkDeleteWarning('?action=network-delete&amp;network=".$tmp2['name']."')\" href=\"#\">Delete</a>";
            //$act .= " | <a onclick=\"networkDeleteWarning('$deleteURL','$currentURL')\" href=\"#\">Delete</a>";
            //$act .= " | <a href=\"?action=network-delete&amp;network=$networkName\">Delete</a>";
            $act .= " | <a onclick=\"networkDeleteWarning('$deleteURL', '$networkName')\" href=\"#\"> Delete </a>";
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

function networkDeleteWarning(linkURL, fileName) {
    var r = confirm("Deleting network " + fileName + ".");
    if (r == true) {
      window.location = linkURL;
    }
}
</script>

<?php
require('../footer.php');
?>
