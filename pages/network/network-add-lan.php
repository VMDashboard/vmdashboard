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
  $_SESSION['forward_mode'] = $_POST['forward_mode'];
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
  $forward_mode = $_SESSION['forward_mode'];
  $mac_address = $_SESSION['mac_address'];
  $ip_address = $_SESSION['ip_address'];
  $subnet_mask = $_SESSION['subnet_mask'];
  $dhcp_service = $_SESSION['dhcp_service'];
  $dhcp_start_address = $_SESSION['dhcp_start_address'];
  $dhcp_end_address = $_SESSION['dhcp_end_address'];
  unset($_SESSION['network_name']);
  unset($_SESSION['forward_mode']);
  unset($_SESSION['mac_address']);
  unset($_SESSION['ip_address']);
  unset($_SESSION['subnet_mask']);
  unset($_SESSION['dhcp_service']);
  unset($_SESSION['dhcp_start_address']);
  unset($_SESSION['dhcp_end_address']);

  $xml = "
  <network>
    <name>$network_name</name>
    <forward mode='$forward_mode'/>
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

  $ret = $lv->network_define_xml($xml)? 'success' : 'Error while creating network: '.$lv->get_last_error();
  if ($ret == 'success') {
    header('Location: network-list.php');
    exit;
  }

} //end if $_SESSION

require('../navbar.php');
$random_mac = $lv->generate_random_mac_addr();


$tmp = $lv->get_networks(VIR_NETWORKS_ALL);
$new_network_name = "network";

for ($i = 0; $i < sizeof($tmp); $i++) {
  $numbers_in_string = countEndingDigits( $network ); //Counts how many digits are at the end of the string
  if ( $numbers_in_string > 0 ) :
	   $base_portion = substr( $new_network_name, 0, -$numbers_in_string );
	   $digits_portion = abs(substr( $new_network_name, -$numbers_in_string ));
  else :
	   $base_portion = $new_network_name;
	   $digits_portion = '';
  endif;

  $tmp2 = $lv->get_network_information($tmp[$i]);
  if ($tmp2['name'] == $base_portionv . $digits_portion){
    $new_network_name = $base_portion . ($digits_portion + 1);
  }
}

?>

<script>
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

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Add virtual network to <?php echo $hn; ?></h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#networkLAN" role="tab" data-toggle="tab">Private Network</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="networkLAN">

                <div class="row">
                  <label class="col-sm-2 col-form-label">Network Name: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" value="newNETWORK" required="required" placeholder="Enter name for new network connection " class="form-control" name="network_name" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Forward Mode: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select class="form-control" name="forward_mode">
                        <option value="nat" selected>nat</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">MAC Address: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" class="form-control" name="mac_address" value="<?php echo $random_mac; ?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Gateway IP Address: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" class="form-control" name="ip_address" value="192.168.1.1" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Subnet Mask: </label>
                  <div class="col-sm-7">
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
                  <label class="col-sm-2 col-form-label">DHCP Service: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select class="form-control" name="dhcp_service" onchange="dhcpChangeOptions(this)">
                        <option value="enabled" selected>enabled</option>
                        <option value="disabled">disabled</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">DHCP Starting Address: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" class="form-control" name="dhcp_start_address" value="192.168.1.2" placeholder="Enter starting IP address or none"/>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">DHCP Ending Address: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" class="form-control" name="dhcp_end_address" value="192.168.1.254" placeholder="Enter ending IP address or none"/>
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
