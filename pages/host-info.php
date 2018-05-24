<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}
// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: login.php');
}
// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['action'] = $_POST['action'];
    $_SESSION['name'] = $_POST['name'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
// Time to bring in the header, sidebar, and navigation menus
require('header.php');
require('navigation.php');
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Host</h3>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Host: <?php echo $hn; ?></h2>

          <div class="clearfix"></div>
        </div>

        <div class="x_content">

          <div class="col-md-9 col-sm-9 col-xs-12">
            <?php

            // Time to get all information on the host
            $tmp = $lv->host_get_node_info();

            // Let's start the $ret without any data, it will be used to display returned XML info
            $ret = false;

            // Check for SESSION variables to see if we need to retieve XML data
            if (array_key_exists('action', $_SESSION)) {
              $name = $_SESSION['name'];
              if ($_SESSION['action'] == 'dumpxml')
                $ret = 'XML dump of node device <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_node_device_xml($name, false));
                //Unset the SESSION variables
                unset($_SESSION['name']);
                unset($_SESSION['action']);
            }

            //If we have returned XML data, display it
            if ($ret){
              echo "<pre>$ret</pre>";
              echo "<br /><br />";
            }



            $ci  = $lv->get_connect_information();
            $info = '';
            if ($ci['uri'])
                $info .= ' <i>'.$ci['uri'].'</i> on <i>'.$ci['hostname'].'</i>, ';

            if (strlen($info) > 2)
                $info[ strlen($info) - 2 ] = ' ';

                echo "<h2> Host Information </h2> <br>";
                echo "<strong>Hypervisor:</strong> {$ci['hypervisor_string']} <br>";
                echo "<strong>Connection:</strong> $info <br>";
                echo "<strong>Architecture:</strong> {$tmp['model']} <br>";
                echo "<strong>Total memory installed:</strong> " . number_format(($tmp['memory'] / 1048576), 2, '.', ' ') . " GB <br>";
                echo "<strong>Total processor count:</strong> {$tmp['cpus']} <br>";
                echo "<strong>Processor speed:</strong> {$tmp['mhz']} MHz <br>";
                echo "<strong>Processor nodes:</strong> {$tmp['nodes']} <br>";
                echo "<strong>Processor sockets:</strong> {$tmp['sockets']} <br>";
                echo "<strong>Processor cores:</strong> {$tmp['cores']} <br>";
                echo "<strong>Processor threads:</strong> {$tmp['threads']} <br> <br> <br>";



            //Time to retrieve the information about the host and place it in a table
            $tmp = $lv->get_node_device_cap_options();
            for ($i = 0; $i < sizeof($tmp); $i++) {

              //Just pull out SYSTEM data
              if ($tmp[$i] == "system"){
                echo "<h4>{$tmp[$i]}</h4>";
                $tmp1 = $lv->get_node_devices($tmp[$i]);
                echo "<div class='table-responsive'>" .
                  "<table class='table'>" .
                  "<tr>" .
                  "<th> Hardware Vendor </th>" .
                  "<th> Product </th>" .
                  "<th> Serial </th>" .
                  "<th> Firmware Vendor </th>" .
                  "<th> Firmware Version </th>" .
                  "<th> Firmware Release Date </th>" .
                  "<th> Action </th>" .
                  "</tr>";

                for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                  $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                  //Actions will be a form button that will submit info using POST
                  $act = "<form method=\"post\" action=\"\">
                    <input type=\"hidden\" name=\"action\" value=\"dumpxml\">
                    <input type=\"hidden\" name=\"name\" value=\"{$tmp2['name']}\">
                    <input type=\"submit\" name=\"submit\" value=\"XML\">
                    </form>";



                    $vendor = array_key_exists('hardware_vendor', $tmp2) ? $tmp2['hardware_vendor'] : 'Unknown';
                    $product_name = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';
                    $serial = array_key_exists('hardware_serial', $tmp2) ? $tmp2['hardware_serial'] : 'Unknown';
                    $firmware_vender = array_key_exists('firmware_vendor', $tmp2) ? $tmp2['firmware_vendor'] : 'Unknown';
                    $firmware_version = array_key_exists('firmware_version', $tmp2) ? $tmp2['firmware_version'] : 'Unknown';
                    $firmware_release_date = array_key_exists('firmware_release_date', $tmp2) ? $tmp2['firmware_release_date'] : 'Unknown';


                  echo "<tr>" .
                    "<td>$vendor</td>" .
                    "<td>$product_name</td>" .
                    "<td>$serial</td>" .
                    "<td>$tmp2['firmware_vendor']</td>" .
                    "<td>$firmware_version</td>" .
                    "<td>$firmware_release_date</td>" .
                    "<td>$act</td>" .
                    "</tr>";

                }

                echo "</table></div>";

              }
            }

            ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        <!-- /page content -->

<?php require('footer.php');?>
