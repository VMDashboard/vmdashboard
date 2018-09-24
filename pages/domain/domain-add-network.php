<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

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

  header("Location: ".$_SERVER['PHP_SELF']."?uuid=".$uuid);
  exit;
}

if (isset($_SESSION['network_type'])) {
  $network_type = $_SESSION['network_type'];
  $mac = $_SESSION['mac'];
  $network = $_SESSION['network'];
  $source_dev = $_SESSION['source_dev'];
  $model_type = $_SESSION['model_type'];

  if ($network_type == "network")
    $notification = $lv->domain_nic_add($domName, $mac, $network, $model_type) ? "" : "Cannot add network to the guest: ".$lv->get_last_error();

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

    $notification = $lv->domain_change_xml($domName, $newXML) ? "" : "Cannot add network to the guest: ".$lv->get_last_error();
  }

  unset($_SESSION['network_type']);
  unset($_SESSION['mac']);
  unset($_SESSION['network']);
  unset($_SESSION['source_dev']);
  unset($_SESSION['model_type']);

  //Return back to the domain-single page if successful
  if (!$notification){
    header("Location: domain-single.php?uuid=".$uuid);
    exit;
  }
}

require('../navbar.php');

?>

<div class="content">
  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">device_hub</i>
            </div>
            <h3 class="card-title">Add Network Device</h3>
            <p class="card-category">Virtual Machine: <?php echo $domName; ?></p>
          </div>
          <div class="card-body">
            <br />
            <div class="row">
              <label class="col-3 col-form-label">Network Type: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="network_type" onchange="networkChangeOptions(this)">
                    <option value="network" selected>nat</option>
                    <option value="direct">bridged</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label networkChange" id="network">Private Network: </label>
              <div class="col-6">
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
              <label class="col-3 col-form-label">Model Type: </label>
              <div class="col-6">
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
              <label class="col-3 col-form-label networkChange" id="direct" style="display:none;">Host Interface: </label>
              <div class="col-6">
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
              <label class="col-3 col-form-label" id="direct" style="display:none;">Mode: </label>
              <div class="col-6">
                <div class="form-group" id="direct" style="display:none;">
                  <input type="text" class="form-control" readonly="readonly" name="source_mode" value="bridge">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">MAC Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <?php $random_mac = $lv->generate_random_mac_addr();?>
                  <input type="text" value="<?php echo $random_mac; ?>" required="required" id="DataImageName" placeholder="Enter MAC Address" class="form-control" name="mac" />
                </div>
              </div>
            </div>

          </div> <!-- end card body -->
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-warning">Submit</button>
          </div>
        </form>
      </div> <!-- end card -->
    </div>
  </div> <!-- end row -->
</div> <!-- end content -->

<script>
window.onload =  function() {
  <?php
  if ($notification) {
    echo "showNotification(\"top\",\"right\",\"$notification\");";
  }
  ?>
}

function showNotification(from, align, text){
    color = 'warning';
    $.notify({
        icon: "",
        message: text
      },{
          type: color,
          timer: 500,
          placement: {
              from: from,
              align: align
          }
      });
}

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

<?php
require('../footer.php');
?>
