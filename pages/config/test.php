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


      </div> <!-- end card body -->
    </form>
  </div> <!-- end card -->


  <div class="col-md-3">
        <div class="card ">
          <div class="card-header ">
            <h5 class="card-title">Email Statistics</h5>
            <p class="card-category">Last Campaign Performance</p>
          </div>
          <div class="card-body ">
            <canvas id="chartDonut1" class="ct-chart ct-perfect-fourth" width="456" height="300"></canvas>
          </div>
          <div class="card-footer ">
            <div class="legend">
              <i class="fa fa-circle text-info"></i> Open
            </div>
            <hr>
            <div class="stats">
              <i class="fa fa-calendar"></i> Number of emails sent
            </div>
          </div>
        </div>
      </div>



</div> <!-- end content -->





<script>
ctx = document.getElementById('chartDonut1').getContext("2d");

    myChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: [1, 2],
        datasets: [{
          label: "Emails",
          pointRadius: 0,
          pointHoverRadius: 0,
          backgroundColor: ['#4acccd', '#f4f3ef'],
          borderWidth: 0,
          data: [60, 40]
        }]
      },
      options: {
        elements: {
          center: {
            text: '60%',
            color: '#66615c', // Default is #000000
            fontStyle: 'Arial', // Default is Arial
            sidePadding: 60 // Defualt is 20 (as a percentage)
          }
        },
        cutoutPercentage: 90,
        legend: {

          display: false
        },

        tooltips: {
          enabled: false
        },

        scales: {
          yAxes: [{

            ticks: {
              display: false
            },
            gridLines: {
              drawBorder: false,
              zeroLineColor: "transparent",
              color: 'rgba(255,255,255,0.05)'
            }

          }],

          xAxes: [{
            barPercentage: 1.6,
            gridLines: {
              drawBorder: false,
              color: 'rgba(255,255,255,0.1)',
              zeroLineColor: "transparent"
            },
            ticks: {
              display: false,
            }
          }]
        },
      }
    });
</script>

<?php
require('../footer.php');
?>
