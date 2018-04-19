<?php
require('header.php');

function clean_name_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $network_name = clean_name_input($_POST['network_name']);
  $forward_mode = $_POST['forward_mode'];
  $mac_address = $_POST['mac_address'];
  $ip_address = clean_name_input($_POST['ip_address']);
  $subnet_mask = $_POST['subnet_mask'];
  $dhcp_service = $_POST['dhcp_service'];
  $dhcp_start_address = clean_name_input($_POST['dhcp_start_address']);
  $dhcp_end_address = clean_name_input($_POST['dhcp_end_address']);


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
}

require('navigation.php');

$random_mac = $lv->generate_random_mac_addr();
?>

<?php
//alert
if ($ret) {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
?>

<script>
function dhcpChangeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;
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

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Networking</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Create new network</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a></li>
                  <li><a href="#">Settings 2</a></li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">
            <!-- Smart Wizard -->
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
              <div class="form-horizontal form-label-left" style="min-height: 250px;">

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="network_name">Network name <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" value="newNETWORK" required="required" placeholder="Enter name for new network connection " class="form-control" name="network_name" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="forward_mode" class="control-label col-md-3 col-sm-3 col-xs-12">Forward Mode</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" name="forward_mode">
                      <option value="nat" selected>nat</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mac_address">MAC Address <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" name="mac_address" value="<?php echo $random_mac; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ip_address">Gateway IP Address <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" name="ip_address" value="192.168.1.1" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="subnet_mask" class="control-label col-md-3 col-sm-3 col-xs-12">Subnet Mask</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" name="subnet_mask">
                      <option value="255.255.255.0" selected>/24 -> 255.255.255.0</option>
                      <option value="255.255.255.128">/25 -> 255.255.255.128</option>
                      <option value="255.255.255.128">/26 -> 255.255.255.192</option>
                      <option value="255.255.255.128">/27 -> 255.255.255.224</option>
                      <option value="255.255.255.128">/28 -> 255.255.255.240</option>
                      <option value="255.255.255.128">/29 -> 255.255.255.248</option>
                      <option value="255.255.255.128">/30 -> 255.255.255.252</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="dhcp_service" class="control-label col-md-3 col-sm-3 col-xs-12">DHCP Service</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" name="dhcp_service" onchange="dhcpChangeOptions(this)">
                      <option value="enabled" selected>enabled</option>
                      <option value="disabled">disabled</option>
                    </select>
                  </div>
                </div>

                <div class="form-group dhcpChange">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dhcp_start_address">DHCP Starting Address <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" name="dhcp_start_address" value="192.168.1.2" placeholder="Enter starting IP address or none"/>
                  </div>
                </div>

                <div class="form-group dhcpChange">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dhcp_end_address">DHCP Ending Address <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" name="dhcp_end_address" value="192.168.1.254" placeholder="Enter ending IP address or none"/>
                  </div>
                </div>

              </div>

              <div class="actionBar">
                <input type="submit" name="submit" class="buttonFinish btn btn-default" value="Finish" />
              </div>

            </form>
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
