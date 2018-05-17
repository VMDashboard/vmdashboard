<?php
session_start();
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
          <h2>Host: <?php echo $hn; ?></h2>

          <div class="clearfix"></div>
        </div>

        <div class="x_content">

          <div class="col-md-9 col-sm-9 col-xs-12">
            <?php
            $tmp = $lv->host_get_node_info();
            $ret = false;

            if (array_key_exists('action', $_GET)) {
              $name = $_GET['name'];
              if ($_GET['action'] == 'dumpxml')
                $ret = 'XML dump of node device <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_node_device_xml($name, false));
            }

            if ($ret){
              echo "<pre>$ret</pre>";
              echo "<br /><br />";
            }

            $tmp = $lv->get_node_device_cap_options();
            for ($i = 0; $i < sizeof($tmp); $i++) {
              if ($tmp[$i] == "net"){
                echo "<h4>{$tmp[$i]}</h4>";
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
                  $act = !array_key_exists('cap', $_GET) ? "<a href=\"?action={$_GET['action']}&amp;action=dumpxml&amp;name={$tmp2['name']}\">Dump configuration</a>" :
                    "<a href=\"?action=dumpxml&amp;cap={$_GET['cap']}&amp;name={$tmp2['name']}\">Dump configuration</a>";

                  $ident = array_key_exists('interface_name', $tmp2) ? $tmp2['interface_name'] : '-';
                  $driver = array_key_exists('capabilities', $tmp2) ? $tmp2['capabilities'] : '-';
                  $vendor = 'Unknown';
                  $product = 'Unknown';

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
</div>
        <!-- /page content -->

<?php require('footer.php');?>
