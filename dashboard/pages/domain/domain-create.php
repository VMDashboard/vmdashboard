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

// This function is used to prevent any problems with user form input
function clean_input($data) {
  $data = trim($data); //remove spaces at the beginning and end of string
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data); //remove any spaces within the string
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $_SESSION['domain_type'] = "kvm"; //set to "kvm" as this is the only supported type at this time
  //$_SESSION['domain_name'] = clean_input($_POST['domain_name']); //removes spaces and sanitizes
  $_SESSION['memory_unit'] = $_POST['memory_unit']; //choice of "MiB" or "GiB"
  $_SESSION['memory'] = $_POST['memory']; //number input, still need to sanitze for number and verify it is not zero
  $_SESSION['vcpu'] = $_POST['vcpu']; //number input, still need to sanitze for number and verify it is not zero, also may need to set limit to host CPU#
  $_SESSION['os_arch'] = "x86_64"; //set to x86_64, need to change to host type as well as provide options
  $_SESSION['os_type'] = "hvm"; //hvm is standard operating system VM
  $_SESSION['clock_offset'] = "localtime"; //set to localtime
  $_SESSION['os_platform'] = $_POST['os_platform'];
  $_SESSION['source_file_volume'] = $_POST['source_file_volume']; //This will be the volume image that the user selects
  //$_SESSION['volume_image_name'] = clean_input($_POST['new_volume_name']); //This is used when a new volume must be created
  $_SESSION['volume_capacity'] = $_POST['new_volume_size'];
  $_SESSION['unit'] = $_POST['new_unit'];
  $_SESSION['volume_size'] = $_POST['new_volume_size'];
  $_SESSION['driver_type'] = $_POST['new_driver_type'];
  $_SESSION['source_file_cd'] = $_POST['source_file_cd'];
  $_SESSION['interface_type'] = $_POST['interface_type'];
  $_SESSION['mac_address'] = $_POST['mac_address'];
  $_SESSION['source_dev'] = $_POST['source_dev'];
  $_SESSION['source_mode'] = $_POST['source_mode'];
  $_SESSION['source_network'] = $_POST['source_network'];

  //header("Location: ".$_SERVER['PHP_SELF']);
  //exit;
}






  //Graphics Information
  $graphics_type = "vnc";
  $graphics_port = "-1";
  $autoport = "yes";


  //Final XML
  $xml = "
    <domain type='" . $domain_type . "'>
    <name>" . $domain_name . "</name>
    <description>
      " . $os_platform . " platform
    </description>
    <memory unit='" . $memory_unit . "'>" . $memory . "</memory>
    <vcpu>" . $vcpu . "</vcpu>

    <os>
      <type arch='" . $os_arch . "'>" . $os_type . "</type>
      <boot dev='hd'/>
      <boot dev='cdrom'/>
      <boot dev='network'/>
    </os>

    " . $features . "

    <cpu mode='custom' match='exact'>
      <model fallback='allow'>Nehalem</model>
    </cpu>

    <clock offset='" . $clock_offset . "'/>

    <devices>
      " . $volume_xml . "
      " . $cd_xml . "
      " . $network_interface_xml . "
      <graphics type='" . $graphics_type . "' port='" . $graphics_port . "' autoport='" . $autoport . "'/>
      <memballoon model='virtio'>
            <stats period='10'/>
      </memballoon>
    </devices>
    </domain> ";

  //Define the new guest domain based off the XML information
  $new_domain = $lv->domain_define($xml);

  //need to check to make sure $new_domain is not false befoe this code exectues
  if ($source_file_volume == "new") {
    $res = $new_domain;
    $img = libvirt_storagevolume_get_path($new_disk);
    $dev = $target_dev_volume;
    $typ = $target_bus_volume;
    $driver = $driver_type;
    $ret = $lv->domain_disk_add($res, $img, $dev, $typ, $driver);
  }

  //Will display a sweet alert if a return message exists
  if ($ret != "") {
    echo "
      <script>
        var alert_msg = '$ret'
        swal(alert_msg);
      </script>";
  }

  unset($_SESSION['domain_type']);
  unset($_SESSION['domain_name']);
  unset($_SESSION['memory_unit']);
  unset($_SESSION['memory']);
  unset($_SESSION['vcpu']);
  unset($_SESSION['os_arch']);
  unset($_SESSION['os_type']);
  unset($_SESSION['clock_offset']);
  unset($_SESSION['os_platform']);
  unset($_SESSION['source_file_volume']);
  unset($_SESSION['new_volume_name']);
  unset($_SESSION['new_volume_size']);
  unset($_SESSION['new_unit']);
  unset($_SESSION['new_volume_size']);
  unset($_SESSION['new_driver_type']);
  unset($_SESSION['source_file_cd']);
  unset($_SESSION['interface_type']);
  unset($_SESSION['mac_address']);
  unset($_SESSION['source_dev']);
  unset($_SESSION['source_mode']);
  unset($_SESSION['source_network']);

  header('Location: domain-list.php');
  exit;
}


$random_mac = $lv->generate_random_mac_addr(); //used to set default mac address value in form field
require('../navbar.php');
?>

<script>
function diskChangeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;
    if (selectedValue.charAt(0) === "/") {
      selectedValue = "existing";
    }
  let subForms = document.getElementsByClassName('diskChange')
  for (let i = 0; i < subForms.length; i += 1) {
    if (selectedValue === subForms[i].id) {
      subForms[i].setAttribute('style', 'display:block')
    } else {
      subForms[i].setAttribute('style', 'display:none')
    }
  }
}

function autoDiskName(f) {
  var diskName = f.domain_name.value;
  diskName = diskName.replace(/\s+/g, '');
  var ext = ".qcow2";
  var fullDiskName = diskName.concat(ext);
  f.new_volume_name.value = fullDiskName;
}

function newExtenstion(f) {
  var diskName = f.new_volume_name.value;
  diskName = diskName.replace(/\s+/g, '');
  var n = diskName.lastIndexOf(".");
  var noExt = n > -1 ? diskName.substr(0, n) : diskName;
  var driverType = f.new_driver_type.value;
  if (driverType === "qcow2"){
    var ext = ".qcow2";
    var fullDiskName = noExt.concat(ext);
    f.new_volume_name.value = fullDiskName;
  }
  if (driverType === "raw"){
    var ext = ".img";
    var fullDiskName = noExt.concat(ext);
    f.new_volume_name.value = fullDiskName;
  }
}

function changeOptions(selectEl) {
  let selectedValue = selectEl.options[selectEl.selectedIndex].value;
  let subForms = document.getElementsByClassName('netChange')
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
    <div class="card-header">
      <h4 class="card-title"> New Virtual Machine</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-4 col-6">
          <div class="nav-tabs-navigation verical-navs">
            <div class="nav-tabs-wrapper">
              <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#info" role="tab" data-toggle="tab">Info</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#description" role="tab" data-toggle="tab">Description</a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-md-7 col-sm-8 col-6">
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="info">
              <p>Larger, yet dramatically thinner. More powerful, but remarkably power efficient. With a smooth metal surface that seamlessly meets the new Retina HD display.</p>
              <p>It’s one continuous form where hardware and software function in perfect unison, creating a new generation of phone that’s better by any measure.</p>
            </div>
            <div class="tab-pane" id="description">
              <p>The first thing you notice when you hold the phone is how great it feels in your hand. The cover glass curves down around the sides to meet the anodized aluminum enclosure in a remarkable, simplified design. </p>
              <p>There are no distinct edges. No gaps. Just a smooth, seamless bond of metal and glass that feels like one continuous surface.</p>
            </div>

          </div>
        </div>
      </div>


    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
