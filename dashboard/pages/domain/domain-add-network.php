<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
//if (!isset($_SESSION['username'])){
//  header('Location: login.php');
//}

require('../header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);

$domXML = new SimpleXMLElement($lv->domain_get_xml($domName));
$os_platform = $domXML->description;



// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $_SESSION['network_type'] = $_POST['network_type'];
  $_SESSION['mac'] = $_POST['mac'];
  $_SESSION['network'] = $_POST['network'];
  $_SESSION['source_dev'] = $_POST['source_dev'];
  $_SESSION['model_type'] = $_POST['model_type'];

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

if (isset($_SESSION['network_type'])) {
  $network_type = $_SESSION['network_type'];
  $mac = $_SESSION['mac'];
  $network = $_SESSION['network'];
  $source_dev = $_SESSION['source_dev'];
  $model_type = $_SESSION['model_type'];

  if ($network_type == "network")
    $ret = $lv->domain_nic_add($domName, $mac, $network, $model_type) ? "success" : "Cannot add network to the guest: ".$lv->get_last_error();

  if ($network_type == "direct"){
    $domXML = $lv->domain_get_xml($domName);
    $domXML = new SimpleXMLElement($domXML);
    if ($model_type == "default") {
      $model_type = "virtio";
    }

    //add a new interface
    $interface = $domXML->devices->addChild('interface');
    $interface->addAttribute('type','direct');
    $mac_address = $interface->addChild('mac');
    $mac_address->addAttribute('address', $mac);
    $source = $interface->addChild('source');
    $source->addAttribute('dev', $source_dev);
    $source->addAttribute('mode','bridge');
    $model = $interface->addChild('model');
    $model->addAttribute('type',$model_type);

    $newXML = $domXML->asXML();
    $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);

    $ret = $lv->domain_change_xml($domName, $newXML); //third param is flags
  }

  unset($_SESSION['network_type']);
  unset($_SESSION['mac']);
  unset($_SESSION['network']);
  unset($_SESSION['source_dev']);
  unset($_SESSION['model_type']);

  //Return back to the domain-single page
  //header("Location: domain-single.php?uuid=".$uuid);
  header( "Location:domain-single.php? uuid = $uuid" );
  exit;
}

require('../navbar.php');

?>

<script>
function networkChangeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;

  let subForms = document.getElementsByClassName('networkChange')
  for (let i = 0; i < subForms.length; i += 1) {
    if (selectedValue === subForms[i].id) {
      subForms[i].setAttribute('style', 'display:block')
    } else {
      subForms[i].setAttribute('style', 'display:none')
    }
  }
}
</script>

<div class="content">
  <div class="card">
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Add network device to <?php echo $domName; ?></h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#network" role="tab" data-toggle="tab">Network</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="optical">

                <div class="row">
                  <label class="col-sm-2 col-form-label">Network Type: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select class="form-control" name="network_type" onchange="networkChangeOptions(this)">
                        <option value="network" selected>nat</option>
                        <option value="direct">bridged</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label networkChange" id="network">Private Network: </label>
                  <div class="col-sm-7">
                    <div class="form-group networkChange" id="network">
                      <select class="form-control" name="network">
                        <?php
                        $networks = $lv->get_networks();
                        for ($i = 0; $i < sizeof($networks); $i++) {
                          echo "<option value=\"$networks[$i]\"> $networks[$i] </option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Model Type: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select class="form-control" name="model_type">
                        <?php
                        if ($os_platform == "Windows platform"){
                          echo "<option value=\"rtl8139\">rtl8139</option>";
                        } else {
                          $models = $lv->get_nic_models();
                          for ($i = 0; $i < sizeof($models); $i++) {
                            echo "<option value=\"$models[$i]\"> $models[$i] </option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label networkChange" id="direct" style="display:none;">Host Interface: </label>
                  <div class="col-sm-7">
                    <div class="form-group networkChange" id="direct" style="display:none;">
                      <select class="form-control" name="source_dev">
                        <?php
                        $tmp = $lv->get_node_device_cap_options();
                        for ($i = 0; $i < sizeof($tmp); $i++) {
                          $tmp1 = $lv->get_node_devices($tmp[$i]);
                          for ($ii = 0; $ii < sizeof($tmp1); $ii++) {
                            $tmp2 = $lv->get_node_device_information($tmp1[$ii]);
                            if ($tmp2['capability'] == 'net') {
                              $ident = array_key_exists('interface_name', $tmp2) ? $tmp2['interface_name'] : 'N/A';
                              echo "<option value='$ident'> $ident </option>";
                            }
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label networkChange" id="direct" style="display:none;">Mode: </label>
                  <div class="col-sm-7">
                    <div class="form-group networkChange" id="direct" style="display:none;">
                      <input type="text" class="form-control" readonly="readonly" name="source_mode" value="bridge">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">MAC Address: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <?php $random_mac = $lv->generate_random_mac_addr();?>
                      <input type="text" value="<?php echo $random_mac; ?>" required="required" id="DataImageName" placeholder="Enter MAC Address" class="form-control" name="mac" />
                    </div>
                  </div>
                </div>


              </div> <!-- end tab pane -->
            </div> <!-- end tab content -->
          </div>

        </div>
      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-danger">Submit</button>
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
