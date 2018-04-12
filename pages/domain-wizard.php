<<?php
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
        <h3>Domain Wizard</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Create new virtual machine</h2>
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
              <p>This wizard will guide you through the steps to create a new guest virtual machine.</p>
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
                        <form class="form-horizontal form-label-left">

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="domain_name">Domain Name <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" id="domain_name" required="required" class="form-control col-md-7 col-xs-12" value="newVM" onkeyup="autoDiskName(this.form)" placeholder="Enter a Unique Virtual Machine Name (required)" name="domain_name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vcpu">Virtual CPUs <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="number" id="vcpu" name="vcpu" required="required" class="form-control col-md-7 col-xs-12" min="1" value="1">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="memory">Memory <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="number" id="vcpu" name="memory" required="required" class="form-control col-md-7 col-xs-12" min="1" value="2">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Memory Unit</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <div id="memory_unit" class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                  <input type="radio" name="memory_unit" value="MiB">MB
                                </label>
                                <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-primary">
                                  <input type="radio" name="memory_unit" value="GiB" checked="checked"> GB
                                </label>
                              </div>
                            </div>
                          </div>




                          <div class="form-group">
                            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Middle Name / Initial</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="middle-name" class="form-control col-md-7 col-xs-12" type="text" name="middle-name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <div id="gender" class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                  <input type="radio" name="gender" value="male"> &nbsp; Male &nbsp;
                                </label>
                                <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                  <input type="radio" name="gender" value="female"> Female
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Date Of Birth <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="birthday" class="date-picker form-control col-md-7 col-xs-12" required="required" type="text">
                            </div>
                          </div>

                        </form>

                      </div>
                      <div id="step-2">
                        <h2 class="StepTitle">Step 2 Content</h2>
                        <p>
                          do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                          fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                        <p>
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                          in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                      </div>
                      <div id="step-3">
                        <h2 class="StepTitle">Step 3 Content</h2>
                        <p>
                          sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
                          eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                        <p>
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                          in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                      </div>

                    </div>
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
