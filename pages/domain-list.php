<?php
require('header.php');
require('navigation.php');

$uuid = $_GET['uuid']; //grab the $uuid variable from $_GET, only used for actions below
$action = $_GET['action']; //grab the $action variable from $_GET
$domName = $lv->domain_get_name_by_uuid($uuid); //get the name of virtual machine with $uuid is present
$dom = $lv->get_domain_object($domName); //gets the resource id for a domain

//This will turn a shutdown virtual machine on. This option in only given when a machine is shutdown
if ($action == 'domain-start') {
  $msg = $lv->domain_start($domName) ? "Domain has been started successfully" : 'Error while starting domain: '.$lv->get_last_error();
  if($msg) {
    echo "<script>
    new PNotify({
      title: 'Regular Success',
      text: 'Domain may have started!',
      type: 'success',
      styling: 'bootstrap3'
    });
    </script>";
  }
}

//This will pause a virtual machine and temporaily save it's state
if ($action == 'domain-pause') {
  $msg = $lv->domain_suspend($domName) ? "Domain has been paused successfully" : 'Error while pausing domain: '.$lv->get_last_error();
}

//This will resume a paused virtual machine. Option is given only if a machine is paused
if ($action == 'domain-resume') {
  $msg = $lv->domain_resume($domName) ? "Domain has been resumed successfully" : 'Error while resuming domain: '.$lv->get_last_error();
}

//This is used to gracefully shutdown the guest.
//There are many reasons why a guest cannot gracefully shutdown so if it can't, let the user know that
if ($action == 'domain-stop') {
  $msg = $lv->domain_shutdown($domName) ? "Domain has been stopped successfully" : 'Error while stopping domain: '.$lv->get_last_error();
  $actioninfo = $lv->domain_get_info($dom);
  $actionstate = $lv->domain_state_translate($actioninfo['state']);
  if ($actionstate == "running"){
    $msg = "Domain is unable to shutdown gracefully. It will need to be forcefully turned off";
  }
}

//This will forcefully shutdown the virtual machine guest
if ($action == 'domain-destroy') {
  $msg = $lv->domain_destroy($domName) ? "Domain has been destroyed successfully" : 'Error while destroying domain: '.$lv->get_last_error();
}
 ?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Domain List</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Guests</h2>
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

            <script>
            PNotify({
                                  title: 'Regular Notice',
                                  text: 'Check me out! I\'m a notice.',
                                  styling: 'bootstrap3'
                              });
            </script>


            <!-- start project list -->
            <table class="table table-striped projects">
              <thead>
                <tr>
                  <th style="width: 1%">#</th>
                  <th style="width: 20%">Guest Name</th>
                  <th>Virtual CPUs</th>
                  <th>Memory</th>
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
                      "<td>#</td>" .
                      "<td>" .
                        "<a href=\"domain-single.php?uuid=$uuid\">$name</a><br />" .
                        "<small>State:$state</small>" .
                      "</td>" .
                      "<td>$cpu</td>" .
                      "<td>$mem</td>" .
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
<script>
   $(document).ready(function (){
           $('.ui-pnotify').remove();
   });
</script>
