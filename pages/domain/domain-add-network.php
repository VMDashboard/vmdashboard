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
//$os_platform = $domXML->description; //Not using this anymore, however may need to do this in the future


// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $_SESSION['mac_address'] = $_POST['mac_address'];
  $_SESSION['source_network'] = $_POST['source_network'];
  $_SESSION['model_type'] = $_POST['model_type'];
  $_SESSION['action'] = "ADD NETWORK";

  header("Location: ".$_SERVER['PHP_SELF']."?uuid=".$uuid);
  exit;
}

if ($_SESSION['action'] == "ADD NETWORK") {
  $mac_address = $_SESSION['mac_address'];
  $source_network = $_SESSION['source_network'];
  $model_type = $_SESSION['model_type'];
  
  $notification = $lv->domain_nic_add($domName, $mac_address, $source_network, $model_type) ? "" : "Cannot add network to the guest: ".$lv->get_last_error();
 
  unset($_SESSION['mac_address']);
  unset($_SESSION['source_network']);
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
            <h3 class="card-title">Add Network Adapter</h3>
            <p class="card-category">Virtual Machine: <?php echo $domName; ?></p>
          </div>
          <div class="card-body">
            <br />
            
            <div class="row">
              <label class="col-3 col-form-label">MAC Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <?php $random_mac = $lv->generate_random_mac_addr();?>
                  <input type="text" value="<?php echo $random_mac; ?>" required="required" id="DataImageName" placeholder="Enter MAC Address" class="form-control" name="mac_address" />
                </div>
              </div>
            </div>
            
            <div class="row">
              <label class="col-3 col-form-label">Model Type: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="model_type">
                    <?php
                      $models = $lv->get_nic_models();
                      for ($i = 0; $i < sizeof($models); $i++) {
                        echo "<option value=\"$models[$i]\"> $models[$i] </option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Network Source: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="source_network">
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
