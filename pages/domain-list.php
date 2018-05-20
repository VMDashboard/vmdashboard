<?php
require('header.php');
require('navigation.php');

$uuid = $_GET['uuid']; //grab the $uuid variable from $_GET, only used for actions below
$action = $_GET['action']; //grab the $action variable from $_GET
$domName = $lv->domain_get_name_by_uuid($uuid); //get the name of virtual machine with $uuid is present
$dom = $lv->get_domain_object($domName); //gets the resource id for a domain

//This will turn a shutdown virtual machine on. This option in only given when a machine is shutdown
if ($action == 'domain-start') {
  $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
}

//This will pause a virtual machine and temporaily save it's state
if ($action == 'domain-pause') {
  $ret = $lv->domain_suspend($domName) ? "Domain has been paused successfully" : 'Error while pausing domain: '.$lv->get_last_error();
}

//This will resume a paused virtual machine. Option is given only if a machine is paused
if ($action == 'domain-resume') {
  $ret = $lv->domain_resume($domName) ? "Domain has been resumed successfully" : 'Error while resuming domain: '.$lv->get_last_error();
}

//This is used to gracefully shutdown the guest.
//There are many reasons why a guest cannot gracefully shutdown so if it can't, let the user know that
if ($action == 'domain-stop') {
  $ret = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
  $actioninfo = $lv->domain_get_info($dom); //gets domain info, will be used to get the running state
  $actionstate = $lv->domain_state_translate($actioninfo['state']); //get the action state from the info
  //If actionstate is running that means that the domain could not shutdown and will need to be forcefully powered off
  if ($actionstate == "running"){
    $ret = "Domain is unable to shutdown gracefully. It will need to be forcefully turned off";
  }
}

//This will forcefully shutdown the virtual machine guest
if ($action == 'domain-destroy') {
  $ret = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
}
?>

<?php
//alert the user of any ret messages
if ($ret) {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Domains</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Domain List</h2>
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

            <p><a href="domain-wizard.php"><i class="fa fa-plus"></i> Create new guest </a></p>

            <!-- start project list -->
            <table class="table table-striped projects">
              <thead>
                <tr>
                  <th style="width: 1%">#</th>
                  <th style="width: 20%">Guest Name</th>
                  <th>Virtual CPUs</th>
                  <th>CPU Stats</th>
                  <th>Memory</th>
                  <th>Memory Stats</th>
                  <th>Disks</th>
                  <th>State</th>
                  <th style="width: 20%">#Actions</th>
                </tr>
              </thead>
              <tbody>

                <tbody>
                  <?php
                  $doms = $lv->get_domains();
                  foreach ($doms as $name) {
                    $dom = $lv->get_domain_object($name);
                    $uuid = libvirt_domain_get_uuid_string($dom);
                    $active = $lv->domain_is_active($dom);
                    $info = $lv->domain_get_info($dom);
                    $mem = number_format($info['memory'] / 1024, 0, '.', '').' MB';
                    $mem_stats = $lv->domain_get_memory_stats($name);
                    $mem_used = (1- $mem_stats[4]/$mem_stats[5])*100;
                    if ($mem_stats != false && !isset($mem_stats[4]) && !isset($mem_stats[5])){
                      $mem_used = 100;
                    }
                    $cpu = $info['nrVirtCpu'];

                    //Getting the first set of CPU stats
                    $cpu_info_0 = shell_exec("virsh domstats --cpu-total $name");
                    //Sleep for 1 second
                    usleep(1000);
                    //Getting the second set of CPU stats, approximately 1 second later
                    $cpu_info_1 = shell_exec("virsh domstats --cpu-total $name");
                    //Need to seperate the string
                    $cpu_info_0_exploded = explode(" ", $cpu_info_0);
                    //Getting the first CPU time
                    $cpu_time_0 = explode("=", $cpu_info_0_exploded[3]);
                    //Seperating the second string
                    $cpu_info_1_exploded = explode(" ", $cpu_info_1);

                    $cpu_time_1 = explode("=", $cpu_info_1_exploded[3]);

                    $cpu_percentage = ($cpu_time_1[1] - $cpu_time_0[1])/24000000*100;


                    $state = $lv->domain_state_translate($info['state']);
                    $id = $lv->domain_get_id($dom);
                    $arch = $lv->domain_get_arch($dom);
                    $vnc = $lv->domain_get_vnc_port($dom);
                    $nics = $lv->get_network_cards($dom);

  	                if (($diskcnt = $lv->get_disk_count($dom)) > 0) {
                      $disks = $diskcnt.' / '.$lv->get_disk_capacity($dom);
                      $diskdesc = 'Current physical size: '.$lv->get_disk_capacity($dom, true);
                    } else {
                      $disks = '-';
                      $diskdesc = '';
                    }

                    unset($tmp);
                    unset($dom);

  	                echo "<tr>" .
                      "<td> -> </td>" .
                      "<td> <a href=\"domain-single.php?uuid=$uuid\"> $name </a> </td>" .
                      "<td> $cpu </td>" .
                      "<td> $cpu_percentage </td>" .
                      "<td> $mem </td>" .
                      "<td>
                        <div class=\"progress\">
                          <div class=\"progress-bar progress-bar-info\" data-transitiongoal=\"$mem_used\"></div>
                        </div>
                      </td>" .
                      "<td title='$diskdesc'>$disks</td>" .
                      "<td>$state</td>" .
                      "<td>";

  	                if ($lv->domain_is_running($name)){
                     echo "<a href=\"?action=domain-stop&amp;uuid=$uuid\">Shutdown</a> | <a href=\"?action=domain-destroy&amp;uuid=$uuid\">Turn off</a> | <a href=\"?action=domain-pause&amp;uuid=$uuid\">Pause</a>";
                    } else if ($lv->domain_is_paused($name)){
                     echo "<a href=\"?action=domain-resume&amp;uuid=$uuid\">Resume</a>";
                    } else {
                     echo "<a href=\"?action=domain-start&amp;uuid=$uuid\"><i class=\"fa fa-power-off\"></i> Power on</a>";
                    }
                    echo "</td>";
  	                echo "</tr>";
                  }
                  ?>
                </tr>
              </tbody>
            </table>
            <!-- end project list -->

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
