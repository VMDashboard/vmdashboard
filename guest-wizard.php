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
  $domain_type = "kvm"; //set to "kvm"
  $domain_name = clean_name_input($_POST['domain_name']); //removes spaces and sanitizes
  $memory_unit = $_POST['memory_unit']; //choice of "MiB" or "GiB"
  $memory = $_POST['memory']; //number input, still need to sanitze for number and verify it is not zero
  $vcpu = $_POST['vcpu']; //number input, still need to sanitze for number and verify it is not zero, also may need to set limit to host CPU#
  $os_arch = "x86_64"; //set to x86_64, need to change to host type as well as provide options
  $os_type = "hvm"; //hvm is standard operating system VM
  $clock_offset = "localtime"; //set to localtime

  //Hard drive information
  $disk_type_vda = "file";
  $disk_device_vda = "disk";
  $driver_name_vda = "qemu";
  $source_file_vda = $_POST['source_file_vda']; //This will be the volume image that the user selects
  $target_dev_vda = "vda";
  $target_bus_vda = "virtio";

  //determine disk file extension to determine driver type
  $dot_array = explode('.', $source_file_vda);
  $extension = end($dot_array);
  if ($extension == "qcow2") {
    $driver_type_vda = "qcow2";
  } else {
    $driver_type_vda = "raw";
  }

  //determine what the hard drive xml will be
  switch ($source_file_vda) {
    case "none":
      $vda_xml = "";
      break;

    case "new":
      $pool = "default";
      $volume_image_name = clean_name_input($_POST['new_volume_name']);
      //Lets check for empty string
      if ($volume_image_name == "") {
        $volume_image_name = $domain_name . "-volume-image";
      }
      $volume_capacity = $_POST['new_volume_size'];
      $unit = $_POST['new_unit'];
      $volume_size = $_POST['new_volume_size'];
      $driver_type = $_POST['new_driver_type'];
      $new_disk = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type);
      $vda_xml = "";
      break;

    default:
      $vda_xml = "
      <disk type='" . $disk_type_vda . "' device='" . $disk_device_vda . "'>
      <driver name='" . $driver_name_vda . "' type='" . $driver_type_vda . "'/>
      <source file='" . $source_file_vda . "'/>
      <target dev='" . $target_dev_vda . "' bus='" . $target_bus_vda . "'/>
      </disk>";
  }


  //CD-DVD ISO Information
  $disk_type_cd = "file";
  $disk_device_cd = "cdrom";
  $driver_name_cd = "qemu";
  $driver_type_cd = "raw";
  $source_file_cd = $_POST['source_file_cd'];
  $target_dev_cd = "hda";
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
$interface_type = $_POST['interface_type'];
$mac_address = $_POST['mac_address'];
$source_dev = $_POST['source_dev'];
$source_mode = $_POST['source_mode'];
$model_type = $_POST['model_type'];
$source_network = $_POST['source_network'];

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
    <memory unit='" . $memory_unit . "'>" . $memory . "</memory>
    <vcpu>" . $vcpu . "</vcpu>

    <os>
      <type arch='" . $os_arch . "'>" . $os_type . "</type>
      <boot dev='hd'/>
      <boot dev='cdrom'/>
      <boot dev='network'/>
    </os>

    <clock offset='" . $clock_offset . "'/>

    <devices>
      " . $vda_xml . "
      " . $cd_xml . "
      " . $network_interface_xml . "
      <graphics type='" . $graphics_type . "' port='" . $graphics_port . "' autoport='" . $autoport . "'/>
    </devices>
    </domain> ";

  //Define the new guest domain based off the XML information
  $new_domain = $lv->domain_define($xml);

//need to check to make sure $new_domain is not false befoe this code exectues
if ($source_file_vda == "new") {
  $res = $new_domain;
  $img = libvirt_storagevolume_get_path($new_disk);
  $dev = "vda"; //because it is virtio type, will choose the "v" and this should be first disk so "vda"
  $typ = "virtio";
  $driver = $driver_type;
  $ret = $lv->domain_disk_add($res, $img, $dev, $typ, $driver);
}

header('Location: guests.php');
exit;
}

$random_mac = $lv->generate_random_mac_addr(); //used to set default mac address value in form field
require('navbar.php'); //bring in sidebar and page layout
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

<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Create a new guest VM</h3>
            <h5 class="description">This wizard will guide you through setting up a new domain.</h5>

            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link active" href="#general" data-toggle="tab">
                    <i class="now-ui-icons education_paper"></i>
                    General
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab">
                    <i class="fas fa-database"></i>
                    Storage
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#networking" data-toggle="tab">
                    <i class="fas fa-sitemap"></i>
                    Networking
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!--    General Tab     -->
              <div class="tab-pane fade show active" id="general">
                <h5 class="info-text"> Let's start with the basic information</h5>
                <div class="row justify-content-center">
                  <!--    Required fields are setup in assets/demo/demo.js     -->
                  <div class="col-sm-7">
                    <div class="form-group">
                      <label>Domain name</label>
                      <input type="text" class="form-control" value="newVM" onkeyup="autoDiskName(this.form)" placeholder="Enter a Unique Virtual Machine Name (required)" name="domain_name" required />
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Virtual CPUs</label>
                      <input type="number" value="1" class="form-control" name="vcpu" min="1" required />
                    </div>
                  </div>

                  <div class="col-sm-7">
                    <div class="form-group">
                      <label>Memory</label>
                      <input type="number" value="2" placeholder="Enter the amount of RAM (required)" class="form-control" name="memory" min="1" required />
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Memory Unit</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Single Select" name="memory_unit" />
                        <option value="MiB"> MB </option>
                        <option value="GiB" selected="selected"> GB </option>
                      </select>
                    </div>
                  </div>

                </div>
              </div>

              <!--    Storage Tab     -->
              <div class="tab-pane fade" id="storage">
                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <h5 class="info-text"> Hard Drive Storage </h5>
                    <div class="row justify-content-center">

                      <div class="col-sm-10">
                        <div class="form-group">
                          <label>Disk drive source file location</label>
                          <select onchange="diskChangeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="source_file_vda">
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

                      <div class="col-sm-10 diskChange" id="new" style="display:none;">
                        <div class="form-group">
                          <label>Disk Image Name</label>
                          <input type="text" id="DataImageName" value="newVM.qcow2" placeholder="Enter new disk name" class="form-control" name="new_volume_name"/>
                        </div>
                      </div>

                      <div class="col-sm-6 diskChange" id="new" style="display:none;">
                        <div class="form-group">
                          <label>Volume size</label>
                          <input type="number" value="40" class="form-control" name="new_volume_size" min="1" />
                        </div>
                      </div>

                      <div class="col-sm-4 diskChange" id="new" style="display:none;">
                        <div class="form-group">
                          <label>Unit size</label>
                          <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Unit Size" name="new_unit">
                            <option value="M">MB</option>
                            <option value="G" selected>GB</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-sm-10 diskChange" id="new" style="display:none;">
                        <div class="form-group">
                          <label>Driver type</label>
                          <select onchange="newExtenstion(this.form)" class="selectpicker" data-style="btn btn-plain btn-round" name="new_driver_type">
                            <option value="qcow2" selected="selected"> qcow2 </option>
                            <option value="raw" > raw </option>
                          </select>
                        </div>
                      </div>

                    </div>
                  </div>

                  <div class="col-md-6">
                    <h5 class="info-text"> CD/DVD Storage </h5>
                    <div class="row justify-content-center">

                      <div class="col-sm-10">
                        <div class="form-group">
                          <label>ISO location for cdrom</label>
                          <select class="selectpicker" data-style="btn btn-plain btn-round" name="source_file_cd">
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
                  </div>

                </div>
              </div>


              <!--    Networking Tab     -->
              <div class="tab-pane fade" id="networking">
                <div class="row justify-content-center">
                  <div class="col-sm-12">
                    <h5 class="info-text"> Networking Interface Setup </h5>
                  </div>

                  <div class="col-sm-7">
                    <div class="form-group">
                      <label>Interface type</label>
                      <select onchange="changeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="interface_type" title="Select Interface Type">
                        <option value="network" selected="selected">nat</option>
                        <option value="direct">bridge</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>MAC address</label>
                      <input type="text" class="form-control" name="mac_address" value="<?php echo $random_mac; ?>">
                    </div>
                  </div>

                  <div class="col-sm-5 netChange" id="direct" style="display:none;">
                    <div class="form-group">
                      <label>Source device</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Network Interface" name="source_dev">
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

                  <div class="col-sm-5 netChange" id="direct" style="display:none;">
                    <div class="form-group">
                      <label>Mode</label>
                      <input type="text" class="form-control" name="source_mode" value="bridge" readonly >
                    </div>
                  </div>

                  <div class="col-sm-10 netChange" id="network">
                    <div class="form-group">
                      <label>Source network</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Private Network" name="source_network">
                      <?php
                      $tmp = $lv->get_networks(VIR_NETWORKS_ALL);
                      for ($i = 0; $i < sizeof($tmp); $i++) {
                        $tmp2 = $lv->get_network_information($tmp[$i]);
                        echo "<option value=\"$tmp2['name']\">$tmp2['name']</option>";
                      ?>
                    </div>
                  </div>

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Model type</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Model" name="model_type">
                        <option value="virtio" selected="selected"> virtio </option>
                        <option value="default" disabled> default </option>
                        <option value="rtl8139"> rtl8139 </option>
                        <option value="e1000"> e1000 </option>
                        <option value="pcnet" disabled> pcnet </option>
                        <option value="ne2k_pci" disabled> ne2k_pci </option>
                      </select>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>

          <div class="card-footer">
            <div class="pull-right">
              <input type='button' class='btn btn-next btn-fill btn-rose btn-wd' name='next' value='Next' />
              <input type='submit' class='btn btn-finish btn-fill btn-rose btn-wd' name='finish' value='Finish' />
            </div>

            <div class="pull-left">
              <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
            </div>

            <div class="clearfix"></div>
          </div>

        </form>
      </div>
    </div> <!-- wizard container -->
  </div>
</div>
<?php
require('footer.php');
?>
