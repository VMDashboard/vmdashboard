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
                                <h4 class="card-title"> Simple Table</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">



<?php
                echo "<h2>Networks</h2>";
        echo "This is the administration of virtual networks. You can see all " .
             "the virtual network being available with their settings. Please " .
             "make sure you're using the right network for the purpose you need " .
             "to since using the isolated network between two or multiple guests " .
             "is providing the sharing option but internet connectivity will be " .
             "disabled. Please enable internet services only on the guests that " .
             "are really requiring internet access for operation like e.g. HTTP " .
             "server or FTP server but you don't need to put the internet access " .
             "to the guest with e.g. MySQL instance or anything that might be " .
             "managed from the web-site. For the scenario described you could " .
             "setup 2 network, internet and isolated, where isolated network " .
             "should be setup on both machine with Apache and MySQL but internet " .
             "access should be set up just on the machine with Apache webserver " .
             "with scripts to remotely connect to MySQL instance and manage it " .
             "(using e.g. phpMyAdmin). Isolated network is the one that's having " .
            "forwarding column set to None.";
        $ret = false;
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
        echo "<h3>List of networks</h3>";
        $tmp = $lv->get_networks(VIR_NETWORKS_ALL);
        echo "<table class='table'>" .
             "<thead class='text-primary'><tr>" .
             "<th>Network name $spaces</th>" .
             "<th>$spaces Network state $spaces</th>" .
             "<th>$spaces Gateway IP Address $spaces</th>" .
             "<th>$spaces IP Address Range $spaces</th>" .
             "<th>$spaces Forwarding $spaces</th>" .
             "<th>$spaces DHCP Range $spaces</th>" .
             "<th>$spaces Actions $spaces</th>" .
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
            if (!$tmp2['active'])
                $act .= ' | <a href="?action='.$_GET['action'].'&amp;subaction=edit&amp;name='. urlencode($tmp2['name']) . '">Edit network</a>';
            echo "<tr>" .
                 "<td>$spaces{$tmp2['name']}$spaces</td>" .
                 "<td align=\"center\">$spaces$activity$spaces</td>" .
                 "<td align=\"center\">$spaces$ip$spaces</td>" .
                 "<td align=\"center\">$spaces$ip_range$spaces</td>" .
                 "<td align=\"center\">$spaces$forward$spaces</td>" .
                 "<td align=\"center\">$spaces$dhcp$spaces</td>" .
                 "<td align=\"center\">$spaces$act$spaces</td>" .
                 "</tr>";
        }
        echo "</table>";
        if ($ret)
            echo "<pre>$ret</pre>";
	?>





                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"> Table on Plain Background</h4>
                                <p class="category"> Here is a subtitle for this table</p>
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
        if ($ret)
            echo "<pre>$ret</pre>";


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
