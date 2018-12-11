<?php

// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['network_name'])) {
  $_SESSION['network_name'] = clean_input($_POST['network_name']);
  $_SESSION['interface_dev'] = $_POST['interface_dev'];

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if (isset($_SESSION['network_name'])) {
  $network_name = $_SESSION['network_name'];

  unset($_SESSION['network_name']);


  $interfaces = "<interface dev='" . $_SESSION['interface_dev'] . "' />";
  unset($_SESSION['interface_dev']);

  $xml = " 
  <network>
    <name>$network_name</name>
    <forward mode='bridge'>
      $interfaces
    </forward>
  </network>
  ";

  
  $network_add = $lv->network_define_xml($xml);
 
  if (!$network_add){
    $notification = "Error defining macvtap: " . $lv->get_last_error();
    $notification = filter_var($notification,FILTER_SANITIZE_SPECIAL_CHARS); //Error message will contain special characters
  }
  
  //Return back to the domain-single page if successful
  if (!$notification){
    header('Location: network-list.php');
    exit;
  }

} //end if $_SESSION

require('../navbar.php');
$random_mac = $lv->generate_random_mac_addr();

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
            <h3 class="card-title">Create Private Network</h3>
            <p class="card-category">Local Area Network Settings</p>
          </div>
          <div class="card-body">
            <br />
            <div class="row">
              <label class="col-3 col-form-label">Network Name: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="macvtap" required="required" placeholder="Enter name for new network connection " class="form-control" name="network_name" />
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Network Adapters: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="interface_dev">
                    <option value="eno1">eno1</option>
                    <option value="eno2">eno2</option>
                    <option value="eno3">eno3</option>
                    <option value="eno4">eno4</option>
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

function dhcpChangeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;
    if (selectedValue.charAt(0) === "/") {
      selectedValue = "enabled";
    }
  let subForms = document.getElementsByClassName('dhcpChange')
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
