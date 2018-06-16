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
<style>
.tim-row {
  margin-bottom: 20px;
}

.tim-white-buttons {
  background-color: #777777;
}

.typography-line {
  padding-left: 25%;
  margin-bottom: 35px;
  position: relative;
  display: block;
  width: 100%;
}

.typography-line span {
  bottom: 10px;
  color: #c0c1c2;
  display: block;
  font-weight: 400;
  font-size: 13px;
  line-height: 13px;
  left: 0;
  position: absolute;
  width: 260px;
  text-transform: none;
}

.tim-row {
  padding-top: 60px;
}

.tim-row h3 {
  margin-top: 0;
}

.offline-doc .page-header {
  display: flex;
  align-items: center;
}

.offline-doc .footer {
  position: absolute;
  width: 100%;
  background: transparent;
  bottom: 0;
  color: #fff;
  z-index: 1;
}

#map {
  position: relative;
  width: 100%;
  height: 100vh;
}

.demo-iconshtml {
  font-size: 62.5%;
}

.demo-icons body {
  font-size: 1.6rem;
  font-family: sans-serif;
  color: #333333;
  background: white;
}

.demo-icons a {
  color: #608CEE;
  text-decoration: none;
}

.demo-icons header {
  text-align: center;
  padding: 100px 0 0;
}

.demo-icons header h1 {
  font-size: 2.8rem;
}

.demo-icons header p {
  font-size: 1.4rem;
  margin-top: 1em;
}

.demo-icons header a:hover {
  text-decoration: underline;
}

.demo-icons .nc-icon {
  font-size: 34px;
}

.demo-icons section h2 {
  border-bottom: 1px solid #e2e2e2;
  padding: 0 0 1em .2em;
  margin-bottom: 1em;
}

.demo-icons ul {
  padding-left: 0;
}

.demo-icons ul::after {
  clear: both;
  content: "";
  display: table;
}

.demo-icons ul li {
  width: 20%;
  float: left;
  padding: 16px 0;
  text-align: center;
  border-radius: .25em;
  -webkit-transition: background 0.2s;
  -moz-transition: background 0.2s;
  transition: background 0.2s;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  overflow: hidden;
}

.demo-icons ul li:hover {
  background: #f4f4f4;
}

.demo-icons ul p,
.demo-icons ul em,
.demo-icons ul input {
  display: inline-block;
  font-size: 1rem;
  color: #999999;
  -webkit-user-select: auto;
  -moz-user-select: auto;
  -ms-user-select: auto;
  user-select: auto;
  white-space: nowrap;
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  cursor: pointer;
}

.demo-icons ul p {
  padding: 20px 0 0;
  font-size: 12px;
  margin: 0;
}

.demo-icons ul p::selection,
.demo-icons ul em::selection {
  background: #608CEE;
  color: #efefef;
}

.demo-icons ul em {
  font-size: 12px;
}

.demo-icons ul em::before {
  content: '[';
}

.demo-icons ul em::after {
  content: ']';
}

.demo-icons ul input {
  text-align: center;
  background: transparent;
  border: none;
  box-shadow: none;
  outline: none;
  display: none;
}
</style>

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



<?php
require('../footer.php');
?>

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

          display: true
        },

        tooltips: {
          enabled: true
        },

        scales: {
          yAxes: [{

            ticks: {
              display: true
            },
            gridLines: {
              drawBorder: true,
              zeroLineColor: "transparent",
              color: 'rgba(255,255,255,0.05)'
            }

          }],

          xAxes: [{
            barPercentage: 1.6,
            gridLines: {
              drawBorder: true,
              color: 'rgba(255,255,255,0.1)',
              zeroLineColor: "transparent"
            },
            ticks: {
              display: true,
            }
          }]
        },
      }
    });
</script>
