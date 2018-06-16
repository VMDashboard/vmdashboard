<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  $_SESSION['return_location'] = $_SERVER['PHP_SELF'];
  header('Location: ../login.php');
}

require('../header.php');
require('../navbar.php');
?>

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Test Page</h4>
      </div>
      <div class="card-body">
      <?php
      $info = $lv->host_get_node_info();
      $cpu = $lv->host_get_node_cpu_stats();
      $multi_cpu = $lv->host_get_node_cpu_stats_for_each_cpu();
      $mem = $lv->host_get_node_mem_stats();

      var_dump($mem);
      echo "<br><br><br>";

      $mem_percentage = (($mem['total'] - $mem['free']) / $mem['total']) * 100;
      $mem_percentage = number_format($mem_percentage, 2, '.',',');

      $processor_speed = $info['mhz'] * 1000000;
      $multiplier = $info['nodes'] * $info['cores'];
      $usage0 = $cpu['0']['kernel'] + $cpu['0']['user'];
      $usage1 = $cpu['1']['kernel'] + $cpu['1']['user'];
      $percentage = ($usage1 - $usage0) / ($processor_speed * $multiplier) * 100;
      // PHP: string number_format ( float $number [, int $decimals [, string $dec_point, string $thousands_sep]] )
      $percentage = number_format($percentage, 2, '.', ',' );
      echo "CPU Percentage = " . $percentage . "%<br><br>";
      ?>
      <div class="progress">
        <div class="progress-bar progress-bar-danger" role="progressbar" style="width: <?php echo $percentage . '%'; ?>" aria-valuenow="$mem_used" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <?php
      echo "Memory Percentage = " . $mem_percentage . "%<br><br>";
      ?>
      <div class="progress">
        <div class="progress-bar progress-bar-danger" role="progressbar" style="width: <?php echo $mem_percentage . '%'; ?>" aria-valuenow="$mem_used" aria-valuemin="0" aria-valuemax="100"></div>
      </div>


      </div> <!-- end card body -->
    </form>
  </div> <!-- end card -->

</div> <!-- end content -->



<?php
require('../footer.php');
?>
