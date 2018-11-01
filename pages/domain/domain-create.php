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
  //General Section
  $_SESSION['domain_type'] = "kvm"; //set to "kvm" as this is the only supported type at this time
  $_SESSION['domain_name'] = clean_input($_POST['domain_name']); //removes spaces and sanitizes
  $_SESSION['memory_unit'] = $_POST['memory_unit']; //choice of "MiB" or "GiB"
  $_SESSION['memory'] = $_POST['memory']; //number input, still need to sanitze for number and verify it is not zero
  $_SESSION['vcpu'] = $_POST['vcpu']; //number input, still need to sanitze for number and verify it is not zero, also may need to set limit to host CPU#
  $_SESSION['os_arch'] = $_POST['os_arch']; //VMs will use the same Architecture as the host server
  $_SESSION['os_type'] = "hvm"; //hvm is standard operating system VM
  $_SESSION['clock_offset'] = "localtime"; //set to localtime
  $_SESSION['os_platform'] = $_POST['os_platform']; //Used to determine what goes in XML. Ex. Windows VMs need extra options
  //Storage Volume Section
  $_SESSION['source_file_volume'] = $_POST['source_file_volume']; //This will be the volume image that the user selects
  $_SESSION['volume_image_name'] = clean_input($_POST['new_volume_name']); //This is used when a new volume must be created
  $_SESSION['volume_capacity'] = $_POST['new_volume_size'];
  $_SESSION['unit'] = $_POST['new_unit'];
  $_SESSION['volume_size'] = $_POST['new_volume_size'];
  $_SESSION['driver_type'] = $_POST['new_driver_type'];
  $_SESSION['storage_pool'] = $_POST['storage_pool'];
  //Optical Storage Section
  $_SESSION['source_file_cd'] = $_POST['source_file_cd'];
  //Network Section
  $_SESSION['interface_type'] = $_POST['interface_type'];
  $_SESSION['mac_address'] = clean_input($_POST['mac_address']);
  $_SESSION['source_dev'] = $_POST['source_dev'];
  $_SESSION['source_mode'] = $_POST['source_mode'];
  $_SESSION['source_network'] = $_POST['source_network'];

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

if ($_SESSION['domain_type'] == "kvm") {
  //General Section
  $domain_type = $_SESSION['domain_type'];
  $domain_name = $_SESSION['domain_name'];
  $memory_unit = $_SESSION['memory_unit'];
  $memory = $_SESSION['memory'];
  $vcpu = $_SESSION['vcpu'];
  $os_arch = $_SESSION['os_arch'];
  $os_type = $_SESSION['os_type'];
  $clock_offset = $_SESSION['clock_offset'];
  $os_platform = $_SESSION['os_platform'];
  //Storage Volume Section
  $source_file_volume = $_SESSION['source_file_volume'];
  $volume_image_name = $_SESSION['volume_image_name'];
  $volume_capacity = $_SESSION['volume_capacity'];
  $unit = $_SESSION['unit'];
  $volume_size = $_SESSION['volume_size'];
  $driver_type = $_SESSION['driver_type'];
  $storage_pool = $_SESSION['storage_pool'];
  //Optical Storage Section
  $source_file_cd = $_SESSION['source_file_cd'];
  //Network Section
  $interface_type = $_SESSION['interface_type'];
  $mac_address = $_SESSION['mac_address'];
  $source_dev = $_SESSION['source_dev'];
  $source_mode = $_SESSION['source_mode'];
  $source_network = $_SESSION['source_network'];


  //OS XML Information
  if ($os_platform == "windows") {
    //BIOS Featurs
    $features = "
    <features>
      <acpi/>
      <apic/>
      <hyperv>
        <relaxed state='on'/>
        <vapic state='on'/>
        <spinlocks state='on' retries='8191'/>
      </hyperv>
    </features>";

    //Volume type and bus needed for Windows
    $target_dev_volume = "sda";
    $target_bus_volume = "sata";

    //Networking model for Windows
    $model_type = "rtl8139";
  } else {
    //Features not necessary for Linux or Unix domains
    $features = "
    <features>
      <acpi/>
      <apic/>
    </features>";

    //Linux or Unix domains can use vda and virtio
    $target_dev_volume = "vda";
    $target_bus_volume = "virtio";

    //Networking model for Linux
    $model_type = "virtio";
  }

  //Hard drive information
  $disk_type_volume = "file";
  $disk_device_volume = "disk";
  $driver_name_volume = "qemu";

  //determine disk file extension to determine driver type
  $dot_array = explode('.', $source_file_volume); //seperates string into array based on "."
  $extension = end($dot_array); //end returns the last element in the array, which should be the extension
  if ($extension == "qcow2") {
    $driver_type_volume = "qcow2";
  } else {
    $driver_type_volume = "raw";
  }

  //determine what the hard drive volume xml will be.
  switch ($source_file_volume) {
    case "none":
      $volume_xml = ""; //Will not add a storage volume is "None" is selected
      break;

    case "new":
      $volume_xml = ""; //New storage volumes will be created & added only after successful creating of virtual machine domain
      break;

    default:
      $volume_xml = "
      <disk type='" . $disk_type_volume . "' device='" . $disk_device_volume . "'>
      <driver name='" . $driver_name_volume . "' type='" . $driver_type_volume . "'/>
      <source file='" . $source_file_volume . "'/>
      <target dev='" . $target_dev_volume . "' bus='" . $target_bus_volume . "'/>
      </disk>"; //This option is for adding existing storage volumes to a new virtual machine domain
  }


  //CD-DVD ISO Information
  $disk_type_cd = "file";
  $disk_device_cd = "cdrom";
  $driver_name_cd = "qemu";
  $driver_type_cd = "raw";
  $target_dev_volume == "vda" ? $target_dev_cd = "hda" : $target_dev_cd = "hdb";
  $target_bus_cd = "ide";

  if ($source_file_cd == "none") {
    $cd_xml = "";
  } else {
    $cd_xml = "
      <disk type='" . $disk_type_cd . "' device='" . $disk_device_cd . "'>
      <driver name='" . $driver_name_cd . "' type='" . $driver_type_cd . "'/>
      <source file='" . $source_file_cd . "'/>
      <target dev='" . $target_dev_cd . "' bus='" . $target_bus_cd . "'/>
      <readonly/>
      </disk>";
  }


  //Network Information
  if ($interface_type == "network") {
    $network_interface_xml = "
      <interface type='" . $interface_type . "'>
        <mac address='" . $mac_address . "'/>
        <source network='" . $source_network . "'/>
        <model type='" . $model_type . "'/>
      </interface>";
  }

  if ($interface_type == "direct") {
    $network_interface_xml = "
      <interface type='" . $interface_type . "'>
        <mac address='" . $mac_address . "'/>
        <source dev='" . $source_dev . "' mode='" . $source_mode . "'/>
        <model type='" . $model_type . "'/>
      </interface>";
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

    //This feature is causing error on R710 with ubuntu 16.04 Host <feature policy='require' name='stibp'/>

  //Define the new guest domain based off the XML information
  $new_domain = $lv->domain_define($xml);

  //Check for error on creating the domain
  if (!$new_domain){
    $notification = 'Error creating domain: '.$lv->get_last_error();
  }

  //Create and add storage volume to newly created virtual machine
  if ($source_file_volume == "new" && $new_domain != false) {
    $volume_size = 0;
    $new_disk = $lv->storagevolume_create($storage_pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type);
    if (!$new_disk){
      $notification = $notification . " Error creating disk: ".$lv->get_last_error();
    }
    $res = $new_domain;
    $img = libvirt_storagevolume_get_path($new_disk);
    $dev = $target_dev_volume;
    $typ = $target_bus_volume;
    $driver = $driver_type;
    $new_disk_add = $lv->domain_disk_add($res, $img, $dev, $typ, $driver);
    if (!$new_disk_add){
      $notification = $notification . " Error adding disk: ".$lv->get_last_error();
    }
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
  unset($_SESSION['volume_image_name']);
  unset($_SESSION['volume_capacity']);
  unset($_SESSION['unit']);
  unset($_SESSION['volume_size']);
  unset($_SESSION['new_driver_type']);
  unset($_SESSION['storage_pool']);
  unset($_SESSION['source_file_cd']);
  unset($_SESSION['interface_type']);
  unset($_SESSION['mac_address']);
  unset($_SESSION['source_dev']);
  unset($_SESSION['source_mode']);
  unset($_SESSION['source_network']);

  //Return back to domain-list.php if creation is successful
  if(!$notification) {
    header('Location: ../domain/domain-list.php');
    exit;
  }
}

$random_mac = $lv->generate_random_mac_addr(); //used to set default mac address value in form field

require('../navbar.php');

?>

<div class="content">

  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">phonelink</i>
            </div>
            <h3 class="card-title">Create new virtual machine</h3>
            <p class="card-category">Initial Setup</p>
          </div>
          <div class="card-body">
            <br/> <br />
            <div style="text-align:center;">GENERAL INFO</div>

            <div class="row">
              <label class="col-3 col-form-label">Domain Name: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="domain_name" required="required" value="newVM" onkeyup="autoDiskName(this.form)" placeholder="Enter a Unique Virtual Machine Name (required)" name="domain_name">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">OS Platform: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="os_platform">
                    <option value="linux">Linux</option>
                    <option value="unix">Unix</option>
                    <option value="windows">Windows</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Architecture: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="os_arch">
                    <?php
                    $tmp = $lv->host_get_node_info(); // Get and array of information on the host
                    $arch = $tmp['model']; //Set the Architecture. Used in the General form. Ex., x86_64
                    ?>
                    <option value="<?php echo $arch; ?>"><?php echo $arch; ?></option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Virtual CPUs: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="number" id="vcpu" name="vcpu" required="required" class="form-control" min="1" value="2">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Memory: </label>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" id="vcpu" name="memory" required="required" class="form-control" min="1" value="4">
                </div>
              </div>
              <div class="col-2 checkbox-radios">
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="memory_unit" value="MiB"> MB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="memory_unit" value="GiB" checked="checked"> GB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
              </div>
            </div>

            <br/> <br /> <br /> <br />
            <div style="text-align:center;">DISK VOLUME</div>

            <div class="row">
              <label class="col-3 col-form-label">Source File: </label>
              <div class="col-6">
                <div class="form-group">
                  <select onchange="diskChangeOptions(this)"  class="form-control" name="source_file_volume">
                    <option value="none"> Select Disk </option>
                    <option value="new"> Create New Disk Image </option>
                    <?php
                    $pools = $lv->get_storagepools();
                    for ($i = 0; $i < sizeof($pools); $i++) {
                      $info = $lv->get_storagepool_info($pools[$i]);
                      if ($info['volume_count'] > 0) {
                        $tmp = $lv->storagepool_get_volume_information($pools[$i]);
                        $tmp_keys = array_keys($tmp);
                        for ($ii = 0; $ii < sizeof($tmp); $ii++) {
                          $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
                          $ext = pathinfo($tmp_keys[$ii], PATHINFO_EXTENSION);
                          if (strtolower($ext) != "iso")
                            echo "<option value='" . $tmp[$tmp_keys[$ii]]['path'] . "'>" . $tmp[$tmp_keys[$ii]]['path'] . "</option>";
                        }
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Volume Name: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="new" style="display:none;">
                  <input type="text" class="form-control" id="DataImageName" value="newVM.qcow2" placeholder="Enter new disk name" name="new_volume_name">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Volume Size: </label>
              <div class="col-4 diskChange" id="new" style="display:none;">
                <div class="form-group">
                  <input type="number" class="form-control" value="40" min="1" name="new_volume_size">
                </div>
              </div>
              <div class="col-2 checkbox-radios diskChange" id="new" style="display:none;">
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="new_unit" value="M"> MB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="new_unit" value="G" checked="checked"> GB
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Driver Type: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="new" style="display:none;">
                  <select class="form-control" onchange="newExtenstion(this.form)" name="new_driver_type">
                    <option value="qcow2" selected="selected">qcow2</option>
                    <option value="raw">raw</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Storage Pool: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="new" style="display:none;">
                  <select class="form-control" onchange="newExtenstion(this.form)" name="storage_pool">
                    <?php
                    $counter = 0;
                    for ($i = 0; $i < sizeof($pools); $i++) {
                      //get the pool resource to use with refreshing the pool data
                      $res = $lv->get_storagepool_res($pools[$i]);
                      //refreshing the data before displaying because ISOs were not refreshing automatically and also the Available data was not correct after adding volumes
                      $msg = $lv->storagepool_refresh($res) ? "Pool has been refreshed" : "Error refreshing pool: ".$lv->get_last_error();
                      //getting the pool information to display the data in a table
                      $info = $lv->get_storagepool_info($pools[$i]);
                      $poolName = $pools[$i];

                      $act = $info['active'] ? 'Active' : 'Inactive';
                      if ($act == "Active") {
                        echo "<option value=\"$poolName\">$poolName</option>";
                        $counter++; //Increments only if a valid storage pool exist
                      }
                    }
                    if ($counter == 0) {
                      echo "<option value=\"none\">No storage pools available</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <br/> <br /> <br /> <br />
            <div style="text-align:center;">OPTICAL STORAGE</div>

            <div class="row">
              <label class="col-3 col-form-label">Select File: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="source_file_cd">
                    <option value="none">Select File</option>
                    <?php
                    $pools = $lv->get_storagepools();
                    for ($i = 0; $i < sizeof($pools); $i++) {
                      $info = $lv->get_storagepool_info($pools[$i]);
                      if ($info['volume_count'] > 0) {
                        $tmp = $lv->storagepool_get_volume_information($pools[$i]);
                        $tmp_keys = array_keys($tmp);
                        for ($ii = 0; $ii < sizeof($tmp); $ii++) {
                          $path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
                          $ext = pathinfo($tmp_keys[$ii], PATHINFO_EXTENSION);
                          if (strtolower($ext) == "iso")
                            echo "<option value='" . $tmp[$tmp_keys[$ii]]['path'] . "'>" . $tmp[$tmp_keys[$ii]]['path'] . "</option>";
                        }
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <br/> <br /> <br /> <br />
            <div style="text-align:center;">NETWORKING</div>

            <div class="row">
              <label class="col-3 col-form-label">Interface type: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" onchange="changeOptions(this)" name="interface_type">
                    <option value="network" selected="selected">nat</option>
                    <option value="direct">bridge</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">MAC Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Enter MAC address: 11:22:33:44:55:66" name="mac_address" value="<?php echo $random_mac; ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label netChange" id="direct" style="display:none;">Host Interface: </label>
              <div class="col-6">
                <div class="form-group netChange" id="direct" style="display:none;">
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
              <label class="col-3 col-form-label" id="direct" style="display:none;">Model: </label>
              <div class="col-6">
                <div class="form-group" id="direct" style="display:none;">
                  <input type="text" class="form-control" readonly="readonly" name="source_mode" value="bridge">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label netChange" id="network">Model: </label>
              <div class="col-6">
                <div class="form-group netChange" id="network">
                  <select class="form-control" name="source_network">
                    <?php
                    $tmp = $lv->get_networks(VIR_NETWORKS_ALL);
                    for ($i = 0; $i < sizeof($tmp); $i++) {
                      $tmp2 = $lv->get_network_information($tmp[$i]);
                      echo "<option value='" . $tmp2['name'] . "'>" . $tmp2['name'] . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

          </div>
          <div class="card-footer justify-content-center">
            <div class="stats">
              <button type="submit" class="btn btn-warning">Create</button>
            </div>
          </div>
        </form>
      </div>
    </div>


  </div> <!-- End Row -->
</div>

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

<?php
require('../footer.php');
?>
