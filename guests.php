<?php
require('header.php');
require('navbar.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);

if ($action) {
  $domName = $lv->domain_get_name_by_uuid($_GET['uuid']);

	if ($action == 'domain-start') {
    $ret = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
  }

  if ($action == 'domain-pause') {
    $ret = $lv->domain_suspend($domName) ? "Domain has been paused successfully" : 'Error while pausing domain: '.$lv->get_last_error();
  }

  if ($action == 'domain-resume') {
    $ret = $lv->domain_resume($domName) ? "Domain has been resumed successfully" : 'Error while resuming domain: '.$lv->get_last_error();
  }

  if ($action == 'domain-stop') {
    $ret = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
    $actioninfo = $lv->domain_get_info($dom);
    $actionstate = $lv->domain_state_translate($actioninfo['state']);
    if ($actionstate == "running"){
      $ret = "Domain is unable to shutdown gracefully. It will need to be forcefully turned off";
    }
  }

  if ($action == 'domain-destroy') {
    $ret = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
  }
}


if ($ret != "") {
echo "
<script>
var alertRet = '$ret'
swal(alertRet);
</script>";
}
?>


<div class="panel-header panel-header-sm"></div>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Virtual Machine Guests</h4>
          <a href="guest-wizard.php"><i class="fas fa-plus"></i> Create new guest </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead class=" text-primary">
                <th>Name</th>
                <th>CPU Cores</th>
                <th>Memory</th>
                <th>Disk(s)</th>
                <th>Power State</th>
                <th>Action</th>
              </thead>
              <tbody>
                <?php
                $doms = $lv->get_domains();
                foreach ($doms as $name) {
                  $dom = $lv->get_domain_object($name);
                  $uuid = libvirt_domain_get_uuid_string($dom);
                  $active = $lv->domain_is_active($dom);
                  $info = $lv->domain_get_info($dom);
                  $mem = number_format($info['memory'] / 1024, 0, '.', '').' MB';
                  $cpu = $info['nrVirtCpu'];
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
                    "<td>" .
                    "<a href=\"guest-single.php?uuid=$uuid\">$name</a>" .
                    "</td>" .
                    "<td>$cpu</td>" .
                    "<td>$mem</td>" .
                    "<td title='$diskdesc'>$disks</td>" .
                    "<td>$state</td>";
                   echo "<td>";

	                 if ($lv->domain_is_running($name)){
                     echo "<a href=\"?action=domain-stop&amp;uuid=$uuid\">Shutdown</a> | <a href=\"?action=domain-destroy&amp;uuid=$uuid\">Turn off</a> | <a href=\"?action=domain-pause&amp;uuid=$uuid\">Pause</a>";
                   } else if ($lv->domain_is_paused($name)){
                     echo "<a href=\"?action=domain-resume&amp;uuid=$uuid\">Resume</a>";
                   } else
                   echo "<a href=\"?action=domain-start&amp;uuid=$uuid\"><i class='fas fa-power-off'></i> Power on</a>";
                   echo "</td>" ;
	                  echo "</tr>";
                  }
                  ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require('footer.php');
?>
