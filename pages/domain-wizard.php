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
  $domain_type = "kvm"; //set to "kvm"
  $domain_name = clean_name_input($_POST['domain_name']); //removes spaces and sanitizes
  $memory_unit = $_POST['memory_unit']; //choice of "MiB" or "GiB"
  $memory = $_POST['memory']; //number input, still need to sanitze for number and verify it is not zero
  $vcpu = $_POST['vcpu']; //number input, still need to sanitze for number and verify it is not zero, also may need to set limit to host CPU#
  $os_arch = "x86_64"; //set to x86_64, need to change to host type as well as provide options
  $os_type = "hvm"; //hvm is standard operating system VM
  $clock_offset = "localtime"; //set to localtime
  $os_platform = $_POST['os_platform'];

  //OS Information
  if ($os_platform == "Windows") {
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
      <vmport state='off'/>
    </features>";

    //Volume type and bus needed for Windows
    $target_dev_volume = "hda";
    $target_bus_volume = "ide";

    //Networking model for Windows
    $model_type = "rtl8139";
  } else {
    //Features not necessary for Linux or Unix domains
    $features = "
    <features>
      <acpi/>
      <apic/>
      <vmport state='off'/>
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
  $source_file_volume = $_POST['source_file_volume']; //This will be the volume image that the user selects

  //determine disk file extension to determine driver type
  $dot_array = explode('.', $source_file_volume);
  $extension = end($dot_array);
  if ($extension == "qcow2") {
    $driver_type_volume = "qcow2";
  } else {
    $driver_type_volume = "raw";
  }

  //determine what the hard drive xml will be
  switch ($source_file_volume) {
    case "none":
      $volume_xml = "";
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
      $volume_xml = "";
      break;

    default:
      $volume_xml = "
      <disk type='" . $disk_type_volume . "' device='" . $disk_device_volume . "'>
      <driver name='" . $driver_name_volume . "' type='" . $driver_type_volume . "'/>
      <source file='" . $source_file_volume . "'/>
      <target dev='" . $target_dev_volume . "' bus='" . $target_bus_volume . "'/>
      </disk>";
  }


  //CD-DVD ISO Information
  $disk_type_cd = "file";
  $disk_device_cd = "cdrom";
  $driver_name_cd = "qemu";
  $driver_type_cd = "raw";
  $source_file_cd = $_POST['source_file_cd'];
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
$interface_type = $_POST['interface_type'];
$mac_address = $_POST['mac_address'];
$source_dev = $_POST['source_dev'];
$source_mode = $_POST['source_mode'];
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

header('Location: domain-list.php');
exit;
}

$random_mac = $lv->generate_random_mac_addr(); //used to set default mac address value in form field

require('navigation.php');
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


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Domain</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Create new domain</h2>
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
              <div id="wizard" class="form_wizard wizard_horizontal">
                <ul class="wizard_steps">
                  <li>
                    <a href="#step-1">
                      <span class="step_no">1</span>
                      <span class="step_descr">
                        General<br />
                      </span>
                    </a>
                  </li>
                  <li>
                    <a href="#step-2">
                      <span class="step_no">2</span>
                      <span class="step_descr">
                        Storage<br />
                      </span>
                    </a>
                  </li>
                  <li>
                    <a href="#step-3">
                      <span class="step_no">3</span>
                      <span class="step_descr">
                        Networking<br />
                      </span>
                    </a>
                  </li>
                </ul>

                <div id="step-1">
                  <div class="form-horizontal form-label-left" style="min-height: 250px;">

                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="domain_name">Domain Name <span class="required">*</span>
                      </label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" id="domain_name" required="required" class="form-control col-md-7 col-xs-12" value="newVM" onkeyup="autoDiskName(this.form)" placeholder="Enter a Unique Virtual Machine Name (required)" name="domain_name">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="os_platform" class="control-label col-md-3 col-sm-3 col-xs-12">OS Platform</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <select class="form-control" name="os_platform">
                          <option value="Linux">Linux</option>
                          <option value="Unix">Unix</option>
                          <option value="Windows">Windows</option>
                          <option value="Other">Other</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vcpu">Virtual CPUs <span class="required">*</span>
                      </label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="number" id="vcpu" name="vcpu" required="required" class="form-control col-md-7 col-xs-12" min="1" value="2">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="memory">Memory <span class="required">*</span>
                      </label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="number" id="vcpu" name="memory" required="required" class="form-control col-md-7 col-xs-12" min="1" value="4">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Memory Unit</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <div id="memory_unit" class="btn-group" data-toggle="buttons">
                          <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                            <input type="radio" name="memory_unit" value="MiB"> MB
                          </label>
                          <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-primary active">
                            <input type="radio" name="memory_unit" value="GiB" checked="checked"> GB
                          </label>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

                <div id="step-2">
                  <div class="form-horizontal form-label-left" style="min-height: 250px;">

                    <div class="col-md-6">
                      <h4 class="info-text"> Hard Drive Storage </h4>
                      <div class="row justify-content-center">

                        <div class="form-group">
                          <label for="source_file_volume" class="control-label col-md-3 col-sm-3 col-xs-12">Source File</label>
                          <div class="col-md-9 col-sm-9 col-xs-12">
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

                                <div class="form-group diskChange" id="new" style="display:none;">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Volume Name</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" id="DataImageName" value="newVM.qcow2" placeholder="Enter new disk name" name="new_volume_name">
                                  </div>
                                </div>

                                <div class="form-group diskChange" id="new" style="display:none;">
                                  <label for="new_volume_size" class="control-label col-md-3 col-sm-3 col-xs-12">Volume Size</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="number" class="form-control" value="40" min="1" name="new_volume_size">
                                  </div>
                                </div>

                                <div class="form-group diskChange" id="new" style="display:none;">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Memory Unit</label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div id="new_unit" class="btn-group" data-toggle="buttons">
                                      <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="new_unit" value="M"> MB
                                      </label>
                                      <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-primary active">
                                        <input type="radio" name="new_unit" value="G" checked="checked"> GB
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <div class="form-group diskChange" id="new" style="display:none;">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Driver Type</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" onchange="newExtenstion(this.form)" name="new_driver_type">
                                      <option value="qcow2" selected="selected">qcow2</option>
                                      <option value="raw">raw</option>
                                    </select>
                                  </div>
                                </div>

                            </div>
                          </div>


                          <div class="col-md-6">
                            <h4 class="info-text"> Optical Disk Image </h4>
                            <div class="row justify-content-center">

                                <div class="form-group">
                                  <label for="source_file_volume" class="control-label col-md-3 col-sm-3 col-xs-12">ISO File</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12">
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
                          </div>

                        </div>
                      </div>



                      <div id="step-3">
                        <div class="form-horizontal form-label-left" style="min-height: 250px;">

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Interface type</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <select class="form-control" onchange="changeOptions(this)" name="interface_type">
                                <option value="network" selected="selected">nat</option>
                                <option value="direct">bridge</option>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">MAC Address</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" placeholder="Enter MAC address: 11:22:33:44:55:66" name="mac_address" value="<?php echo $random_mac; ?>">
                            </div>
                          </div>

                          <div class="form-group netChange" id="direct" style="display:none;">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Host interface</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
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


                          <div class="form-group netChange" id="direct" style="display:none;">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Mode</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" readonly="readonly" name="source_mode" value="bridge">
                            </div>
                          </div>



                          <div class="form-group netChange" id="network">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Source Network</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
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

                    </div>
                  </form>
                    <!-- End SmartWizard Content -->

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- jQuery Smart Wizard -->
    <script src="../vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>


  </body>
</html>
