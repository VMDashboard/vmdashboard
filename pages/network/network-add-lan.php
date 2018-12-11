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
  $_SESSION['mac_address'] = $_POST['mac_address'];
  $_SESSION['ip_address'] = clean_input($_POST['ip_address']);
  $_SESSION['subnet_mask'] = $_POST['subnet_mask'];
  $_SESSION['dhcp_service'] = $_POST['dhcp_service'];
  $_SESSION['dhcp_start_address'] = clean_input($_POST['dhcp_start_address']);
  $_SESSION['dhcp_end_address'] = clean_input($_POST['dhcp_end_address']);

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if (isset($_SESSION['network_name'])) {
  $network_name = $_SESSION['network_name'];
  $mac_address = $_SESSION['mac_address'];
  $ip_address = $_SESSION['ip_address'];
  $subnet_mask = $_SESSION['subnet_mask'];
  $dhcp_service = $_SESSION['dhcp_service'];
  $dhcp_start_address = $_SESSION['dhcp_start_address'];
  $dhcp_end_address = $_SESSION['dhcp_end_address'];
  unset($_SESSION['network_name']);
  unset($_SESSION['mac_address']);
  unset($_SESSION['ip_address']);
  unset($_SESSION['subnet_mask']);
  unset($_SESSION['dhcp_service']);
  unset($_SESSION['dhcp_start_address']);
  unset($_SESSION['dhcp_end_address']);

  $xml = "
  <network>
    <name>$network_name</name>
    <forward mode='nat'/>
    <mac address='$mac_address'/>
    <ip address='$ip_address' netmask='$subnet_mask'>
      <dhcp>
        <range start='$dhcp_start_address' end='$dhcp_end_address'/>
      </dhcp>
    </ip>
  </network>";

  if ($dhcp_service == "disabled"){
    $xml = "
    <network>
      <name>$network_name</name>
      <forward mode='$forward_mode'/>
      <mac address='$mac_address'/>
      <ip address='$ip_address' netmask='$subnet_mask'>
      </ip>
    </network>";
  }


  $network_add = $lv->network_define_xml($xml);
 
  if (!$network_add){
    $notification = "Error defining network: " . $lv->get_last_error();
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
                  <input type="text" value="vNetwork" required="required" placeholder="Enter name for new network connection " class="form-control" name="network_name" />
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">MAC Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="mac_address" value="<?php echo $random_mac; ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Gateway IP Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="ip_address" value="192.168.1.1" />
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Subnet Mask: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="subnet_mask">
                    <option value="255.255.255.0" selected>/24 --> 255.255.255.0</option>
                    <option value="255.255.255.128">/25  -->  255.255.255.128</option>
                    <option value="255.255.255.128">/26  -->  255.255.255.192</option>
                    <option value="255.255.255.128">/27  -->  255.255.255.224</option>
                    <option value="255.255.255.128">/28  -->  255.255.255.240</option>
                    <option value="255.255.255.128">/29  -->  255.255.255.248</option>
                    <option value="255.255.255.128">/30  -->  255.255.255.252</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">DHCP Service: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="dhcp_service" onchange="dhcpChangeOptions(this)">
                    <option value="enabled" selected>enabled</option>
                    <option value="disabled">disabled</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">DHCP Starting Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="dhcp_start_address" value="192.168.1.2" placeholder="Enter starting IP address or none"/>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">DHCP Ending Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" name="dhcp_end_address" value="192.168.1.254" placeholder="Enter ending IP address or none"/>
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
