<?php

//Create function that will add domain flags

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
  
  //----General Section----//
  $_SESSION['domain_type'] = "kvm"; //set to "kvm" as this is the only supported type at this time
  $_SESSION['domain_name'] = clean_input($_POST['domain_name']); //removes spaces and sanitizes
  $_SESSION['memory_unit'] = $_POST['memory_unit']; //choice of "MiB" or "GiB"
  $_SESSION['memory'] = $_POST['memory']; //number input, still need to sanitze for number and verify it is not zero
  $_SESSION['vcpu'] = $_POST['vcpu']; //number input, still need to sanitze for number and verify it is not zero, also may need to set limit to host CPU#
  $_SESSION['clock_offset'] = "localtime"; //set to localtime
  $_SESSION['os_platform'] = $_POST['os_platform']; //Used to determine what goes in XML. Ex. Windows VMs need extra options
  
  //----Storage Volume Section----//
  $_SESSION['source_file_volume'] = $_POST['source_file_volume']; //This will be the volume image that the user selects
  $_SESSION['volume_image_name'] = clean_input($_POST['new_volume_name']); //This is used when a new volume must be created
  $_SESSION['volume_capacity'] = $_POST['new_volume_size']; //in Gigabytes
  $_SESSION['volume_size'] = $_POST['new_volume_size']; //in Gigabytes, set to the same size as capacity
  $_SESSION['driver_type'] = $_POST['new_driver_type']; //qcow2 or raw
  $_SESSION['target_bus'] = $_POST['new_target_bus']; //virtia, sata, scsi
  $_SESSION['storage_pool'] = $_POST['storage_pool']; //Where the storage volume will be created
  $_SESSION['existing_driver_type'] = $_POST['existing_driver_type']; //qcow2 or raw for existing storage
  $_SESSION['existing_target_bus'] = $_POST['existing_target_bus']; //virtio, ide, sata, or scsi for existing storage
  
  //----Optical Storage Section----//
  $_SESSION['source_file_cd'] = $_POST['source_file_cd']; //file location is ISO file for booting
  
  //----Network Section----//
  $_SESSION['mac_address'] = clean_input($_POST['mac_address']); //mac address for network device
  $_SESSION['model_type'] = $_POST['model_type']; //virtio, e1000, etc
  $_SESSION['source_network'] = $_POST['source_network']; //default or any created network bridge

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;

} //End if POST data is set


if ($_SESSION['domain_type'] == "kvm") {


  //--------------------- CREATE VIRTUAL MACHINE SECTION ---------------------//

  $domain_type = $_SESSION['domain_type']; //hard coded as "kvm" for now
  $domain_name = $_SESSION['domain_name']; //sanatized name for virtual machine
  $description = "Created by vmdashboard.org"; //plug for software that helped put virtual machine together
  $memory_unit = $_SESSION['memory_unit']; //either MiB or GiB
  $memory = $_SESSION['memory']; //whatever the user sets
  $vcpu = $_SESSION['vcpu']; //whatever the user sets, defaults to 1
  $clock_offset = $_SESSION['clock_offset']; //hard coded as "localtime" for now
  $os_platform = $_SESSION['os_platform']; //determines if bios features need to be set, needed for Windows
  
  $vm_xml = "
    <domain type='$domain_type'>
    <name>$domain_name</name>
    <description>$description</description>
    <memory unit='$memory_unit'>$memory</memory>
    <vcpu>$vcpu</vcpu>
    <os>
    <type>hvm</type>
        <boot dev='hd'/>
        <boot dev='cdrom'/>
        <boot dev='network'/>
    </os>
    <features>
      <acpi/>
      <apic/>
    </features>
    <clock offset='localtime'/>
    <devices>
        <graphics type='vnc' port='-1' autoport='yes'/>
        <video>
          <model type='qxl'/>
        </video>
        <memballoon model='virtio'>
            <stats period='10'/>
        </memballoon>
    </devices>
    </domain> ";


    //--------------------- XML IF WINDOWS VM ---------------------//  

  if ($os_platform == "windows") {
    $vm_xml = "
    <domain type='$domain_type'>
    <name>$domain_name</name>
    <description>$description</description>
    <memory unit='$memory_unit'>$memory</memory>
    <vcpu>$vcpu</vcpu>
    <os>
    <type>hvm</type>
        <boot dev='hd'/>
        <boot dev='cdrom'/>
        <boot dev='network'/>
    </os>
    <features>
      <acpi/>
      <apic/>
      <hyperv>
        <relaxed state='on'/>
        <vapic state='on'/>
        <spinlocks state='on' retries='8191'/>
      </hyperv>
    </features>
    <clock offset='localtime'/>
    <devices>
        <graphics type='vnc' port='-1' autoport='yes'/>
        <video>
          <model type='qxl'/>
        </video>
        <memballoon model='virtio'>
            <stats period='10'/>
        </memballoon>
    </devices>
    </domain> ";

  } 

  $new_vm = $lv->domain_define($vm_xml); //Define the new virtual machine using libvirt, based off the XML information  
  if (!$new_vm){
    $notification = 'Error creating domain: '.$lv->get_last_error(); //let the user know if there is an error
  }


  //--------------------- STORAGE VOLUME SECTION ---------------------//

  $storage_pool = $_SESSION['storage_pool']; //"default" storage pool is default choice
  $volume_image_name = $_SESSION['volume_image_name']; //Sanitized disk name, should end in .qcow2 or .img
  $volume_capacity = $_SESSION['volume_capacity']; //Disk size set by user, defaults to 40
  $unit = "G"; // Gigabytes
  $volume_size = $_SESSION['volume_size'];
  $driver_type = $_SESSION['driver_type'];
  $target_bus = $_SESSION['target_bus'];
  $source_file_volume = $_SESSION['source_file_volume'];
  $existing_driver_type = $_SESSION['existing_driver_type']; //qcow2 or raw
  $existing_target_bus = $_SESSION['existing_target_bus']; //virtio, sata, or scsi

  if ($source_file_volume == "new" && $new_vm != false) {
    $new_disk = $lv->storagevolume_create($storage_pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type);
      
    if (!$new_disk){
      $notification = $notification . " Error creating disk: " . $lv->get_last_error();
    } else {
      $disk_path = libvirt_storagevolume_get_path($new_disk);

      if ($target_bus == "virtio"){
        $target_dev = "vda";
      }

      if ($target_bus == "sata" || $target_bus == "scsi"){
        $target_dev = "sda";
      }

      if ($existing_target_bus == "ide"){
        $target_dev = "hda";
      }

      $res = $new_vm;
      $img = $disk_path;
      $dev = $target_dev;
      $typ = $target_bus;
      $driver = $driver_type;
      $add_new_disk_to_vm = $lv->domain_disk_add($res, $img, $dev, $typ, $driver);
      if (!$add_new_disk_to_vm){
        $notification = $notification . " Error adding disk to virtual machine: ".$lv->get_last_error();
      }
    }
  }

  //Create and add storage volume to newly created virtual machine
  if ($source_file_volume != "none" && $source_file_volume != "new" && $new_vm != false) {

    if ($existing_target_bus == "virtio"){
      $target_dev = "vda";
    }

    if ($existing_target_bus == "sata" || $existing_target_bus == "scsi"){
      $target_dev = "sda";
    }

    if ($existing_target_bus == "ide"){
      $target_dev = "hda";
    }

    $res = $new_vm;
    $img = $source_file_volume;
    $dev = $target_dev;
    $typ = $existing_target_bus;
    $driver = $existing_driver_type;
    $add_existing_disk_to_vm = $lv->domain_disk_add($res, $img, $dev, $typ, $driver);
    if (!$add_existing_disk_to_vm){
      $notification = $notification . " Error adding disk to virtual machine: ".$lv->get_last_error();
    }
  }


//--------------------- OPTICAL STORAGE SECTION ---------------------//

  //Optical Storage Section
  $source_file_cd = $_SESSION['source_file_cd'];

  if ($source_file_cd != "none") {

    $domName = $new_vm;
    $dom = $lv->get_domain_object($domName);
    $domXML = new SimpleXMLElement($lv->domain_get_xml($domName));

    //If $target_bus type is ide then we need to determine highest assigned value of drive, because storage may be using hda ex. hda, hdb, hdc...
    $ide_array = array();
    for ($i = 'a'; $i < 'z'; $i++)
      $ide_array[] = "hd" . $i;

    $tmp = libvirt_domain_get_disk_devices($dom);
    $result = array_intersect($ide_array,$tmp);
    if (count($result) > 0 ) {
      $highestresult = max($result);
      $target_dev = ++$highestresult;
    } else {
      $target_dev = "hda";
    }
  
    //add a new cdrom XML
    $disk = $domXML->devices->addChild('disk');
    $disk->addAttribute('type','file');
    $disk->addAttribute('device','cdrom');
  
    $driver = $disk->addChild('driver');
    $driver->addAttribute('name','qemu');
    $driver->addAttribute('type','raw');
  
    $source = $disk->addChild('source');
    $source->addAttribute('file',$source_file_cd);
  
    $target = $disk->addChild('target');
    $target->addAttribute('dev',$target_dev);
    $target->addAttribute('bus','ide');
  
    $newXML = $domXML->asXML();
    $newXML = str_replace('<?xml version="1.0"?>', '', $newXML);
    
    $add_iso_file = $lv->domain_change_xml($domName, $newXML);
    if (!$add_iso_file){
      $notification = $notification . " Error adding ISO to virtual machine: ".$lv->get_last_error();
    }
  }


  //--------------------- NETWORK SECTION ---------------------//
  //Network Section
  $mac_address = $_SESSION['mac_address'];
  $model_type = $_SESSION['model_type']; //virtio, rtl8139, e1000
  $source_network = $_SESSION['source_network']; //default, br0, etc
  
  //Sets the default network model driver to virtio for Linux Virtual Machines
  if ($_SESSION['os_platform'] == "linux" && $model_type == "default") {
    $model_type = "virtio";
  }

  //Sets the default network model driver to rtl8139 for Windows Virtual Machines
  if ($_SESSION['os_platform'] == "windows" && $model_type == "default") {
    $model_type = "rtl8139";
  }

  //In the future, when application is written to include Apple, change e1000 driver to e1000-82545em driver, works on High Sierra
  if ($_SESSION['os_platform'] == "mac" && $model_type == "default") {
    $model_type = "e1000-82545em";
  }

  $domName = $new_vm;
  $add_nat_network = $lv->domain_nic_add($domName, $mac_address, $source_network, $model_type);
  if (!$add_nat_network){
    $notification = $notification . " Error adding NAT network to virtual machine: ".$lv->get_last_error();
  }
  

  //Remove SESSION varibles created for domain creation
  //General variables
  unset($_SESSION['domain_type']);
  unset($_SESSION['domain_name']);
  unset($_SESSION['memory_unit']);
  unset($_SESSION['memory']);
  unset($_SESSION['vcpu']);
  unset($_SESSION['clock_offset']);
  unset($_SESSION['os_platform']);
  //Storage variables
  unset($_SESSION['source_file_volume']);
  unset($_SESSION['volume_image_name']);
  unset($_SESSION['volume_capacity']);
  unset($_SESSION['volume_size']);
  unset($_SESSION['driver_type']);
  unset($_SESSION['target_bus']);
  unset($_SESSION['storage_pool']);
  unset($_SESSION['existing_driver_type']);
  unset($_SESSION['existing_target_bus']);
  //ISO variables
  unset($_SESSION['source_file_cd']);
  //Network variables
  unset($_SESSION['mac_address']);
  unset($_SESSION['model_type']);
  unset($_SESSION['source_network']);

  //Return back to domain-list.php if creation is successful
  if(!$notification) {
    header('Location: ../domain/domain-list.php');
    exit;
  }
} //Ends if SESSION is "kvm"

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
                    <!--<option value="mac">Macintosh</option>-->
                    <option value="other">Other</option>
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
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Volume Size (GB): </label>
              <div class="col-4 diskChange" id="new" style="display:none;">
                <div class="form-group">
                  <input type="number" class="form-control" value="40" min="1" name="new_volume_size">
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
              <label class="col-3 col-form-label diskChange" id="new" style="display:none;">Target bus: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="new" style="display:none;">
                  <select  class="form-control" name="new_target_bus" >
                    <option value="virtio" selected="selected">virtio</option>
                    <option value="sata">sata</option>
                    <option value="scsi">scsi</option>
                    <option value="ide">ide</option>  
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

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="existing" style="display:none;">Driver type: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="existing" style="display:none;">
                  <select  class="form-control" name="existing_driver_type" >
                    <option value="qcow2" selected="selected">qcow2</option>
                    <option value="raw">raw</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label diskChange" id="existing" style="display:none;">Target bus: </label>
              <div class="col-6">
                <div class="form-group diskChange" id="existing" style="display:none;">
                  <select  class="form-control" name="existing_target_bus" >
                    <option value="virtio" selected="selected">virtio</option>
                    <option value="sata">sata</option>
                    <option value="scsi">scsi</option>
                    <option value="ide">ide</option>
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
              <label class="col-3 col-form-label">MAC Address: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Enter MAC address: 12:34:56:78:9A:BC" name="mac_address" value="<?php echo $random_mac; ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Model Type: </label>
              <div class="col-6">
                <div class="form-group">
                  <select class="form-control" name="model_type">
                    <?php
                      $models = $lv->get_nic_models();
                      for ($i = 0; $i < sizeof($models); $i++) {
                        echo "<option value=\"$models[$i]\"> $models[$i] </option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- List Host Interfaces, may be good to save this for when selecting bridge adapter
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
            -->

            <div class="row">
              <label class="col-3 col-form-label">Network Source: </label>
              <div class="col-6">
                <div class="form-group">
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