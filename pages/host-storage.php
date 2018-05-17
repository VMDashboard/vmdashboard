<?php
session_start()
if (!isset($_SESSION['username'])){
  header('Location: ../index.php');
}
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
                    <h2>Host Storage: <?php echo $hn; ?></h2>
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

                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <?php

                          $tmp = $lv->host_get_node_info();
                          $ci  = $lv->get_connect_information();
                          $info = '';
                          if ($ci['uri'])
                              $info .= ' <i>'.$ci['uri'].'</i> on <i>'.$ci['hostname'].'</i>, ';
                          if ($ci['encrypted'] == 'Yes')
                              $info .= 'encrypted, ';
                          if ($ci['secure'] == 'Yes')
                              $info .= 'secure, ';
                          if ($ci['hypervisor_maxvcpus'])
                              $info .= 'maximum '.$ci['hypervisor_maxvcpus'].' vcpus per guest, ';
                          if (strlen($info) > 2)
                              $info[ strlen($info) - 2 ] = ' ';

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
                                if ($tmp2['capability'] == 'storage') {
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

                                       }

                                       echo "</table></div>";


                               }


                      ?>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

<?php require('footer.php');?>
