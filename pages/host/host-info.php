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
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
require('../header.php');
require('../navbar.php');

$action = $_SESSION['action']; //grab the $action variable from $_SESSION
$name = $_SESSION['name'];
unset($_SESSION['action']); //Unset the Action Variable to prevent repeats of action on page reload
unset($_SESSION['name']);

$info = $lv->host_get_node_info(); //needed to get number of cores and cpu speed for calculations
$cpu_stats = $lv->host_get_node_cpu_stats();
$mem_stats = $lv->host_get_node_mem_stats();

//Determine the percentage of memory used
$mem_percentage = (($mem_stats['total'] - $mem_stats['free']) / $mem_stats['total']) * 100;
$mem_percentage = number_format($mem_percentage, 2, '.',','); //format the percentage to 2 decimal digits

//Determine the percentage of cpu used
$processor_speed = $info['mhz'] * 1000000; //Used to determine how many processor cycles can happen in a second (hertz)
$multiplier = $info['nodes'] * $info['cores']; //Multiplying by the number of phycial cores, not hyperthreaded cores
$usage0 = $cpu_stats['0']['kernel'] + $cpu_stats['0']['user']; //First reading of CPU data
$usage1 = $cpu_stats['1']['kernel'] + $cpu_stats['1']['user']; //Second reading of CPU data one second later
$cpu_percentage = ($usage1 - $usage0) / ($processor_speed * $multiplier) * 100;
$cpu_percentage = number_format($cpu_percentage, 2, '.', ',' ); // PHP: string number_format ( float $number [, int $decimals [, string $dec_point, string $thousands_sep]] )

?>

<div class="content">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"> Host: <?php echo $hn; ?> </h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-3 col-5">
          <div class="nav-tabs-navigation verical-navs">
            <div class="nav-tabs-wrapper">
              <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#general" role="tab" data-toggle="tab">General</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" role="tab" data-toggle="tab">Storage</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#network" role="tab" data-toggle="tab">Network</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#pci" role="tab" data-toggle="tab">PCI</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#usb" role="tab" data-toggle="tab">USB</a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-md-8 col-sm-9 col-7">
          <!-- Tab panes -->
          <div class="tab-content">
            <?php
            $tmp = $lv->host_get_node_info(); // Get and array of information on the host

            // Used when the user clicks the XML link. Will display XML data
            if ($action == 'dumpxml') {
              $ret = 'XML dump of node device <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_node_device_xml($name, false));
            }

            //If we have returned XML data, display it
            if ($ret) {
              echo "<pre>$ret</pre>";
              echo "<br /><br />";
            }

            $ci  = $lv->get_connect_information();
            $info = '';
            if ($ci['uri'])
                $info .= ' <i>'.$ci['uri'].'</i> on <i>'.$ci['hostname'].'</i>, ';
            ?>



            <div class="tab-pane active" id="general">
              <div class="row">

                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <h5>CPU</h5>
                  <strong>CPU Percentage:</strong> <?php echo $cpu_percentage . "%"; ?> <br />
                  <div class="progress">
                    <div class="progress-bar progress-bar-danger" role="progressbar" style="width: <?php echo $cpu_percentage . '%'; ?>" aria-valuenow="<?php echo $cpu_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div> <br />
                  <?php
                  echo "<strong>Total processor count:</strong> {$tmp['cpus']} <br>";
                  echo "<strong>Processor speed:</strong> {$tmp['mhz']} MHz <br>";
                  echo "<strong>Processor nodes:</strong> {$tmp['nodes']} <br>";
                  echo "<strong>Processor sockets:</strong> {$tmp['sockets']} <br>";
                  echo "<strong>Processor cores:</strong> {$tmp['cores']} <br>";
                  echo "<strong>Processor threads:</strong> {$tmp['threads']} <br>";
                  ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <h5>Memory</h5>
                  <strong>Memory Percentage:</strong> <?php echo $mem_percentage . "%"; ?> <br />
                  <div class="progress">
                    <div class="progress-bar progress-bar-danger" role="progressbar" style="width: <?php echo $mem_percentage . '%'; ?>" aria-valuenow="<?php echo $mem_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div> <br />
                  <?php
                  echo "<strong>Total memory installed:</strong> " . number_format(($tmp['memory'] / 1048576), 2, '.', ' ') . " GB <br>";
                  ?>

                </div>
              </div>

              <div class="row">

                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <h5>Hypervisor</h5>
                  <?php
                  if (strlen($info) > 2)
                      $info[ strlen($info) - 2 ] = ' ';

                  echo "<strong>Hypervisor:</strong> {$ci['hypervisor_string']} <br>";
                  echo "<strong>Connection:</strong> $info <br>";
                  echo "<strong>Architecture:</strong> {$tmp['model']} <br>";
                  ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <h5>System</h5>
                  <?php
                  //Time to retrieve the information about the host and place it in a table
                  $tmp = $lv->get_node_device_cap_options();
                  for ($i = 0; $i < sizeof($tmp); $i++) {
                    //Just pull out SYSTEM data
                    if ($tmp[$i] == "system"){
                      $tmp1 = $lv->get_node_devices($tmp[$i]);

                      for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                        $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                        //Actions will be a form button that will submit info using POST
                        $act = "<a title='XML Data' href=\"?action=dumpxml&amp;name={$tmp2['name']}\">XML</a>";

                        $vendor = array_key_exists('hardware_vendor', $tmp2) ? $tmp2['hardware_vendor'] : 'Unknown';
                        $product_name = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';
                        $serial = array_key_exists('hardware_serial', $tmp2) ? $tmp2['hardware_serial'] : 'Unknown';
                        $firmware_vender = array_key_exists('firmware_vendor', $tmp2) ? $tmp2['firmware_vendor'] : 'Unknown';
                        $firmware_version = array_key_exists('firmware_version', $tmp2) ? $tmp2['firmware_version'] : 'Unknown';
                        $firmware_release_date = array_key_exists('firmware_release_date', $tmp2) ? $tmp2['firmware_release_date'] : 'Unknown';

                        echo "<strong>Hardware Vendor:</strong> $vendor <br />";
                        echo "<strong>Product:</strong> $product_name <br />";
                        echo "<strong>Serial:</strong> $serial <br />";
                        echo "<strong>Firmware Vendor:</strong> {$tmp2['firmware_vendor']} <br />";
                        echo "<strong>Firmware Version:</strong> $firmware_vender </br />";
                        echo "<strong>Firmware Release Date: <strong> $firmware_release_date <br />";
                        echo "<strong>Action:</strong> $act <br />";

                      }
                    }
                  }
                  ?>
                </div>

              </div>

            </div>

            <div class="tab-pane" id="storage">
              <?php
              for ($i = 0; $i < sizeof($tmp); $i++) {

                //Just pull out STORAGE data
                if ($tmp[$i] == "storage"){
                  $tmp1 = $lv->get_node_devices($tmp[$i]);
                  echo "<div class='table-responsive'>" .
                    "<table class='table'>" .
                    "<tr>" .
                    "<th> Device name </th>" .
                    "<th> Driver name </th>" .
                    "<th> Vendor </th>" .
                    "<th> Product </th>" .
                    "<th> Action </th>" .
                    "</tr>";

                  for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                    $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                    //Actions will be a form button that will submit info using POST
                    $act = "<a title='XML Data' href=\"?action=dumpxml&amp;name={$tmp2['name']}\">XML</a>";
                    $driver  = array_key_exists('driver_name', $tmp2) ? $tmp2['driver_name'] : 'None';
                    $vendor  = array_key_exists('vendor_name', $tmp2) ? $tmp2['vendor_name'] : 'Unknown';
                    $product = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';

                    echo "<tr>" .
                      "<td>{$tmp2['name']}</td>" .
                      "<td>$driver</td>" .
                      "<td>$vendor</td>" .
                      "<td>$product</td>" .
                      "<td>$act</td>" .
                      "</tr>";
                  }
                  echo "</table></div>";
                }
              }
              ?>
            </div>


            <div class="tab-pane" id="network">
              <?php
              for ($i = 0; $i < sizeof($tmp); $i++) {

                //Just pull out NET data
                if ($tmp[$i] == "net"){
                  $tmp1 = $lv->get_node_devices($tmp[$i]);
                  echo "<div class='table-responsive'>" .
                    "<table class='table'>" .
                    "<tr>" .
                    "<th> Device name </th>" .
                    "<th> Interface </th>" .
                    "<th> Driver name </th>" .
                    "<th> MAC Address </th>" .
                    "<th> Network Speed </th>" .
                    "<th> Action </th>" .
                    "</tr>";

                  for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                    $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                    //Actions will be a form button that will submit info using POST
                    $act = "<a title='XML Data' href=\"?action=dumpxml&amp;name={$tmp2['name']}\">XML</a>";

                    $interface = array_key_exists('interface_name', $tmp2) ? $tmp2['interface_name'] : '-';
                    $driver = array_key_exists('capabilities', $tmp2) ? $tmp2['capabilities'] : '-';
                    $mac_address = array_key_exists('address', $tmp2) ? $tmp2['address'] : '-';
                    $link_speed = "-"; //need to pull from XML file, not available from API

                    echo "<tr>" .
                      "<td>{$tmp2['name']}</td>" .
                      "<td>$interface</td>" .
                      "<td>$driver</td>" .
                      "<td>$mac_address</td>" .
                      "<td>$link_speed</td>" .
                      "<td>$act</td>" .
                      "</tr>";
                  }
                  echo "</table></div>";
                }
              }
              ?>
            </div>


            <div class="tab-pane" id="pci">
              <?php
              for ($i = 0; $i < sizeof($tmp); $i++) {

                //Just pull out PCI data
                if ($tmp[$i] == "pci"){
                  $tmp1 = $lv->get_node_devices($tmp[$i]);
                  echo "<div class='table-responsive'>" .
                    "<table class='table'>" .
                    "<tr>" .
                    "<th> Device name </th>" .
                    "<th> Identification </th>" .
                    "<th> Driver name </th>" .
                    "<th> Vendor </th>" .
                    "<th> Product </th>" .
                    "<th> Action </th>" .
                    "</tr>";

                  for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                    $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                    //Actions will be a form button that will submit info using POST
                    $act = "<a title='XML Data' href=\"?action=dumpxml&amp;name={$tmp2['name']}\">XML</a>";
                    $driver  = array_key_exists('driver_name', $tmp2) ? $tmp2['driver_name'] : 'None';
                    $vendor  = array_key_exists('vendor_name', $tmp2) ? $tmp2['vendor_name'] : 'Unknown';
                    $product = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';

                    if (array_key_exists('vendor_id', $tmp2) && array_key_exists('product_id', $tmp2))
                      $ident = $tmp2['vendor_id'].':'.$tmp2['product_id'];
                    else
                      $ident = '-';

                    echo "<tr>" .
                      "<td>{$tmp2['name']}</td>" .
                      "<td>$ident</td>" .
                      "<td>$driver</td>" .
                      "<td>$vendor</td>" .
                      "<td>$product</td>" .
                      "<td>$act</td>" .
                      "</tr>";
                  }
                  echo "</table></div>";
                }
              }
              ?>
            </div>

            <div class="tab-pane" id="usb">
              <?php
              for ($i = 0; $i < sizeof($tmp); $i++) {

                //Just pull out USB, USB_DEVICE data
                if ($tmp[$i] == "usb_device"){
                  $tmp1 = $lv->get_node_devices($tmp[$i]);
                  echo "<div class='table-responsive'>" .
                    "<table class='table'>" .
                    "<tr>" .
                    "<th> Device name </th>" .
                    "<th> Identification </th>" .
                    "<th> Driver name </th>" .
                    "<th> Vendor </th>" .
                    "<th> Product </th>" .
                    "<th> Action </th>" .
                    "</tr>";

                  for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                    $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                    //Actions will be a form button that will submit info using POST
                    $act = "<a title='XML Data' href=\"?action=dumpxml&amp;name={$tmp2['name']}\">XML</a>";
                    $driver  = array_key_exists('driver_name', $tmp2) ? $tmp2['driver_name'] : 'None';
                    $vendor  = array_key_exists('vendor_name', $tmp2) ? $tmp2['vendor_name'] : 'Unknown';
                    $product = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';

                    if (array_key_exists('vendor_id', $tmp2) && array_key_exists('product_id', $tmp2))
                      $ident = $tmp2['vendor_id'].':'.$tmp2['product_id'];
                    else
                      $ident = '-';

                    echo "<tr>" .
                      "<td>{$tmp2['name']}</td>" .
                      "<td>$ident</td>" .
                      "<td>$driver</td>" .
                      "<td>$vendor</td>" .
                      "<td>$product</td>" .
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
    </form>
  </div>
</div>

<?php
require('../footer.php');
?>
