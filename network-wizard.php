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

if (isset($_POST['finish'])) {
  $network_name = clean_name_input($_POST['network_name']);
  $forward_mode = $_POST['forward_mode'];
  $mac_address = $_POST['mac_address'];
  $ip_address = clean_name_input($_POST['ip_address']);
  $subnet_mask = $_POST['subnet_mask'];
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

  $ret = $lv->network_define_xml($xml);
  if ($ret) {
    header('Location: networking.php');
    exit;
  }
}

require('navbar.php');

$random_mac = $lv->generate_random_mac_addr();
?>

<?php
//alert
if ($ret != "") {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
?>

<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->

          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new network connection</h3>
            <h5 class="description">This form will allow you to create a new network connection.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#network" data-toggle="tab">
                    <i class="fas fa-sitemap"></i>
                        Network
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!--    Network Tab     -->
              <div class="tab-pane fade" id="network">
                <h5 class="info-text"> New Network Connection </h5>
                <div class="row justify-content-center">

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Network Name</label>
                      <input type="text" value="newNETWORK" placeholder="Enter name for new network connection" class="form-control" name="network_name" />
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Forward Mode</label>
                      <select class="selectpicker" onchange="newExtenstion(this.form)" data-style="btn btn-plain btn-round" title="Select forward type" name="forward_mode">
                        <option value="nat" selected>nat</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>MAC Address</label>
                      <input type="text" class="form-control" name="mac_address" value="<?php echo $random_mac; ?>">
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>IP Address</label>
                      <input type="text" class="form-control" name="ip_address" value="192.168.1.1" />
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Subnet Mask</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Subnet Mask" name="subnet_mask">
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

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>DHCP Starting Address</label>
                      <input type="text" class="form-control" name="dhcp_start_address" value="192.168.1.2" />
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>DHCP Ending Address</label>
                      <input type="text" class="form-control" name="dhcp_end_address" value="192.168.1.254" />
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

        <div class="card-footer">
          <div class="pull-right">
            <input type='submit' class='btn btn-finish btn-fill btn-rose btn-wd' name='finish' value='Finish' />
          </div>

          <div class="pull-left"></div>

          <div class="clearfix"></div>
        </div>
      </form>
    </div>
  </div> <!-- wizard container -->
</div>

<?php
require('footer.php');
?>
