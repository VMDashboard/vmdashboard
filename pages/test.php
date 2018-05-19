<?php
require('header.php');

$uuid = "aaa9693e-79dd-4f14-9240-98b30c16b5b2";
$uuid = "ce016179-af13-45a3-880e-b81316d78f4c";
$domName = $lv->domain_get_name_by_uuid($uuid);
$dom = $lv->get_domain_object($domName);
$ret = $lv->domain_get_memory_stats($domName);

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
          var_dump($ret);
          echo "<br><br><br><br>";
          foreach ($ret as $key => $val) {
            echo $key . ": " . $val . "<br>";
          }
          echo "<br><br><br><br>";
          echo "Total memory: " . $ret[5]/1024 . " MB<br>";
          echo "Free memory: " . $ret[4]/1024 . " MB<br>";
          echo "Used memory: " . ($ret[5] - $ret[4])/1024 . " MB<br>";
          echo "Percent used: " . (1- $ret[4]/$ret[5]) * 100 . "% <br>";
          echo "Swap memory used by domain: " . $ret[1] . " KB<br>";
          echo "Usable memory for domain: " . $ret[5] . " KB<br>";
          echo "Assigned memory for domain: " . $ret[6] . " KB<br>";

          $cpuinfo = shell_exec("virsh domstats --cpu-total openstack");
          var_dump($cpuinfo);
          echo "<br><br><br><br>";

          $cpuinfo_0 = shell_exec("virsh domstats --cpu-total openstack");
          for ($i = 0; $i < 1; $i++) {
            $cpuinfo_1 = shell_exec("virsh domstats --cpu-total openstack");
          }
          $cputime0 = explode(" ", $cpuinfo_0);
          echo $cputime0[2];

          ?>



          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php
require('footer.php');
?>
