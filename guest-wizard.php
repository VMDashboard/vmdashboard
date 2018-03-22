<?php
require('header.php');
?>

<?php
//will redirect to guests.php. header() needs to be before navbar.php. Uses libvirst so has to be after header.php
if (isset($_POST['finish'])) {
$domain_type = $_POST['domain_type'];
$domain_name = $_POST['domain_name'];
$memory_unit = $_POST['memory_unit'];
$memory = $_POST['memory'];
$vcpu = $_POST['vcpu'];
$os_arch = $_POST['os_arch'];
$os_type = $_POST['os_type'];
$clock_offset = $_POST['clock_offset'];


//Hard drive information
$disk_type_vda = $_POST['disk_type_vda'];
$disk_device_vda = $_POST['disk_device_vda'];
$driver_name_vda = $_POST['driver_name_vda'];
$driver_type_vda = $_POST['driver_type_vda'];
$source_file_vda = $_POST['source_file_vda'];
$target_dev_vda = $_POST['target_dev_vda'];
$target_bus_vda = $_POST['target_bus_vda'];

if ($source_file_vda == "none") {
  $vda_xml = "";
} else {
  $vda_xml = "
  <disk type='" . $disk_type_vda . "' device='" . $disk_device_vda . "'>
    <driver name='" . $driver_name_vda . "' type='" . $driver_type_vda . "'/>
    <source file='" . $source_file_vda . "'/>
    <target dev='" . $target_dev_vda . "' bus='" . $target_bus_vda . "'/>
  </disk>";
}




//CD-DVD ISO Information
$disk_type_cd = $_POST['disk_type_cd'];
$disk_device_cd = $_POST['disk_device_cd'];
$driver_name_cd = $_POST['driver_name_cd'];
$driver_type_cd = $_POST['driver_type_cd'];
$source_file_cd = $_POST['source_file_cd'];
$target_dev_cd = $_POST['target_dev_cd'];
$target_bus_cd = $_POST['target_bus_cd'];

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
$graphics_type = $_POST['graphics_type'];
$graphics_port = $_POST['graphics_port'];
$autoport = $_POST['autoport'];


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
</domain>
";

$ret = $lv->domain_define($xml) ? "Domain successfully added" : "Cannot add domain: ".$lv->get_last_error();
echo $ret;
header('Location: guests.php');
exit;
}
?>

<?php
require('navbar.php');
?>


<?php
$random_mac = $lv->generate_random_mac_addr();
?>

<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->

          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">
              Create a new guest VM
            </h3>

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
                <li class="nav-item">
                  <a class="nav-link" href="#display" data-toggle="tab">
                    <i class="now-ui-icons tech_tv"></i>
                    Display
                  </a>
                </li>
              </ul>
            </div>
          </div>

                <div class="card-body">
                    <div class="tab-content">
          <!--    General Tab     -->
                        <div class="tab-pane fade show active" id="general">
                          <h5 class="info-text"> Let's start with the basic information (with validation)</h5>
                          <div class="row justify-content-center">

<!--    Required fields are setup in assets/demo/demo.js     -->

                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Domain name</label>
                                    <input type="text" class="form-control" placeholder="Unique Name of Virtual Machine (required)" name="domain_name">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Domain type</label>
                                    <input type="text" value="kvm" class="form-control" name="domain_type"/>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label>Memory</label>
                                    <input type="number" placeholder="Enter the amount of RAM (required)" class="form-control" name="memory"/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Memory Unit</label>
                                    <select class="selectpicker" data-style="btn btn-plain btn-round" title="Single Select" name="memory_unit">
                                        <option value="KiB"> KiB </option>
                                        <option value="MiB" selected="selected"> MiB </option>
                                        <option value="GiB"> GiB </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>OS architecture</label>
                                    <input type="text" value="x86_64" class="form-control" name="os_arch"/>
                                </div>
                            </div>
                            <div class="col-sm-4" style="display:none;">
                                <div class="form-group">
                                    <label>OS type</label>
                                      <input type="text" value="hvm" class="form-control" name="os_type"/>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Virtual CPUs</label>
                                    <input type="text" value="1" class="form-control" name="vcpu"/>
                                </div>
                            </div>
                            <div class="col-sm-5" style="display:none;">
                                <div class="form-group">
                                    <label>Timezone</label>
                                      <input type="text" value="localtime" class="form-control" name="clock_offset"/>
                                </div>
                            </div>

                          </div>
                        </div>

                    <!--    Storage Tab     -->
                    <script>
                    function diskChangeOptions(selectEl) {
                      let selectedValue = selectEl.options[selectEl.selectedIndex].value;
                      let subForms = document.getElementsByClassName('diskChange')
                      for (let i = 0; i < subForms.length; i += 1) {
                        if (selectedValue === subForms[i].id) {
                          subForms[i].setAttribute('style', 'display:block')
                        } else {
                          subForms[i].setAttribute('style', 'display:none')
                        }
                      }
                    }
                    </script>

                        <div class="tab-pane fade" id="storage">
                          <div class="row justify-content-center">
                            <div class="col-md-6">
                            <h5 class="info-text"> Hard Drive Storage </h5>
                            <div class="row justify-content-center">

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Disk type</label>
                                      <input type="text" value="file" class="form-control" name="disk_type_vda" readonly/>
                                  </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Disk device</label>
                                      <input type="text" value="disk" class="form-control" name="disk_device_vda" readonly/>
                                  </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Driver name</label>
                                        <input type="text" value="qemu" class="form-control" name="driver_name_vda"/>
                                  </div>
                              </div>

                              <div class="col-sm-10">
                                  <div class="form-group">
                                      <label>Disk drive source file location</label>
                                      <select onchange="diskChangeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="source_file_vda">
                                        <option value="none"> Select Disk </option>
                                        <option value="new"> New Disk </option>
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
                                      <input type="text" placeholder="Enter new disk name" class="form-control" name="new_target_dev"/>
                                  </div>
                              </div>



                              <div class="col-sm-6 diskChange" id="new" style="display:none;">
                                <div class="form-group">
                                  <label>Volume size</label>
                                  <input type="number" class="form-control" name="new_volume_size" />
                                </div>
                              </div>

                              <div class="col-sm-4 diskChange" id="new" style="display:none;">
                                <div class="form-group">
                                  <label>Unit size</label>
                                  <select class="selectpicker" data-style="btn btn-primary btn-round" title="Select Unit Size" name="new_unit">
                                    <option value="M">MB</option>
                                    <option value="G" selected>GB</option>
                                  </select>
                                </div>
                              </div>

                              <div class="col-sm-10 diskChange" id="new" style="display:none;">
                                  <div class="form-group">
                                      <label>Driver type</label>
                                        <select class="selectpicker" data-style="btn btn-plain btn-round" name="new_driver_type">
                                          <option value="qcow2" selected="selected"> qcow2 </option>
                                          <option value="raw"> raw </option>
                                        </select>
                                  </div>
                              </div>




                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Target device</label>
                                      <input type="text" value="vda" class="form-control" name="target_dev_vda"/>
                                  </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Target bus</label>
                                      <input type="text" value="virtio" class="form-control" name="target_bus_vda"/>
                                  </div>
                              </div>

                            </div>
                          </div>

                            <div class="col-md-6">
                            <h5 class="info-text"> CD/DVD Storage </h5>
                            <div class="row justify-content-center">

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Disk type</label>
                                      <input type="text" value="file" class="form-control" name="disk_type_cd" />
                                  </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Disk device</label>
                                      <input type="text" value="cdrom" class="form-control" name="disk_device_cd" />
                                  </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                  <div class="form-group">
                                      <label>Driver name</label>
                                        <input type="text" value="qemu" class="form-control" name="driver_name_cd"/>
                                  </div>
                              </div>

                              <div class="col-sm-5"  style="display:none;">
                                  <div class="form-group">
                                      <label>Driver type</label>
                                        <input type="text" value="raw" class="form-control" name="driver_type_cd" />
                                  </div>
                              </div>

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


                              <div class="col-sm-5" style="display:none;">
                                <div class="form-group">
                                  <label>Target device</label>
                                  <input type="text" value="hda" class="form-control" name="target_dev_cd"/>
                                </div>
                              </div>

                              <div class="col-sm-5" style="display:none;">
                                <div class="form-group">
                                  <label>Target bus</label>
                                  <input type="text" value="ide" class="form-control" name="target_bus_cd" />
                                </div>
                              </div>



                            </div>
                          </div>
                        </div>
                      </div>




                      <!--    Networking Tab     -->
                      <script>
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
                                        <input type="text" class="form-control" name="source_mode" value="bridge">
                                    </div>
                                </div>

                                <div class="col-sm-10 netChange" id="network">
                                    <div class="form-group">
                                        <label>Source network</label>
                                        <input type="text" class="form-control" name="source_network" value="default">
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

                        <!--    Display Tab     -->
                          <div class="tab-pane fade" id="display">
                              <div class="row justify-content-center">
                                  <div class="col-sm-12">
                                      <h5 class="info-text"> Display options </h5>
                                  </div>


                                  <div class="col-sm-10">
                                      <div class="form-group">
                                          <label>Graphics type</label>
                                          <input type="text" class="form-control" name="graphics_type" value="vnc" />
                                      </div>
                                  </div>

                                  <div class="col-sm-5">
                                      <div class="form-group">
                                          <label>Port</label>
                                          <input type="text" class="form-control" name="graphics_port" value="-1" />
                                      </div>
                                  </div>

                                  <div class="col-sm-5">
                                      <div class="form-group">
                                          <label>Auto Port</label>
                                          <input type="text" class="form-control" name="autoport" value="yes" />
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

<?php
require('footer.php');
?>
