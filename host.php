<?php
require('header.php');
require('navbar.php');

$hn = $lv->get_hostname();
?>


<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"><?php echo $hn; ?></h4>
        </div>
      <div class="card-body">


<?php

    $tmp = $lv->host_get_node_info();
    $ci  = $lv->get_connect_information();
    $info = '';
    if ($ci['uri'])
        $info .= 'connected to <i>'.$ci['uri'].'</i> on <i>'.$ci['hostname'].'</i>, ';
    if ($ci['encrypted'] == 'Yes')
        $info .= 'encrypted, ';
    if ($ci['secure'] == 'Yes')
        $info .= 'secure, ';
    if ($ci['hypervisor_maxvcpus'])
        $info .= 'maximum '.$ci['hypervisor_maxvcpus'].' vcpus per guest, ';
    if (strlen($info) > 2)
        $info[ strlen($info) - 2 ] = ' ';
    echo "<h2>Host information</h2>" .
         "<table>" .
         "<tr>" .
         "<td>Hypervisor: </td>" .
         "<td>{$ci['hypervisor_string']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Connection information: </td>" .
         "<td>$info</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Architecture: </td>" .
         "<td>{$tmp['model']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Total memory installed: </td>" .
         "<td>".number_format(($tmp['memory'] / 1048576), 2, '.', ' ')." GB </td>" .
         "</tr>" .
         "<tr>" .
         "<td>Total processor count: </td>" .
         "<td>{$tmp['cpus']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Processor speed: </td>" .
         "<td>{$tmp['mhz']} MHz</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Processor nodes: </td>" .
         "<td>{$tmp['nodes']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Processor sockets: </td>" .
         "<td>{$tmp['sockets']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Processor cores: </td>" .
         "<td>{$tmp['cores']}</td>" .
         "</tr>" .
         "<tr>" .
         "<td>Processor threads: </td>" .
         "<td>{$tmp['threads']}</td>" .
         "</tr>" .
         "</table>";


         echo "<br /> <br /> <br />";
         echo "<h2>Node devices</h2>";
         echo "Here's the list of node devices available on the host machine. " .
              "You can dump the information about the node devices there so you " .
              "have simple way to check presence of the devices using this " .
              "page.<br/><br/>";
         $ret = false;
         if (array_key_exists('subaction', $_GET)) {
             $name = $_GET['name'];
             if ($_GET['subaction'] == 'dumpxml')
                 $ret = 'XML dump of node device <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_node_device_xml($name, false));
         }

         if ($ret){
             echo "<pre>$ret</pre>";
             echo "<br /><br />";
           }

         $tmp = $lv->get_node_device_cap_options();
         for ($i = 0; $i < sizeof($tmp); $i++) {
           echo "<h3>{$tmp[$i]}</h3>";

           $tmp1 = $lv->get_node_devices($tmp[$i]);
                 echo "<div class='table-responsive'>" .
                      "<table class='table'>" .
                      "<tr>" .
                      "<th> Device name </th>" .
                      "<th>Identification </th>" .
                      "<th> Driver name </th>" .
                      "<th> Vendor </th>" .
                      "<th> Product </th>" .
                      "<th> Action </th>" .
                      "</tr>";
                 for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                     $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                     $act = !array_key_exists('cap', $_GET) ? "<a href=\"?action={$_GET['action']}&amp;subaction=dumpxml&amp;name={$tmp2['name']}\">Dump configuration</a>" :
                         "<a href=\"?action={$_GET['action']}&amp;subaction=dumpxml&amp;cap={$_GET['cap']}&amp;name={$tmp2['name']}\">Dump configuration</a>";
                     if ($tmp2['capability'] == 'system') {
                         $driver = '-';
                         $vendor = array_key_exists('hardware_vendor', $tmp2) ? $tmp2['hardware_vendor'] : '';
                         $serial = array_key_exists('hardware_version', $tmp2) ? $tmp2['hardware_version'] : '';
                         $ident = $vendor.' '.$serial;
                         $product = array_key_exists('hardware_serial', $tmp2) ? $tmp2['hardware_serial'] : 'Unknown';
                     } else if ($tmp2['capability'] == 'net') {
                         $ident = array_key_exists('interface_name', $tmp2) ? $tmp2['interface_name'] : '-';
                         $driver = array_key_exists('capabilities', $tmp2) ? $tmp2['capabilities'] : '-';
                         $vendor = 'Unknown';
                         $product = 'Unknown';
                     } else {
                         $driver  = array_key_exists('driver_name', $tmp2) ? $tmp2['driver_name'] : 'None';
                         $vendor  = array_key_exists('vendor_name', $tmp2) ? $tmp2['vendor_name'] : 'Unknown';
                         $product = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';
                         if (array_key_exists('vendor_id', $tmp2) && array_key_exists('product_id', $tmp2))
                             $ident = $tmp2['vendor_id'].':'.$tmp2['product_id'];
                         else
                             $ident = '-';
                     }
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


              $tmp = libvirt_connect_get_emulator($conn, "x86_64");
              var_dump($tmp);

         }


?>

        </div>
      </div>
    </div>
  </div>
</div>


<?php
require('footer.php');
?>
