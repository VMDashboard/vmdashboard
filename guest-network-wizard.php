<?php
require('header.php');

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
$dom = $lv->get_domain_object($domName);

function clean_name_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

//Grab post infomation and add new drive
if (isset($_POST['finish'])) {
  $mac = "";
  $network = "";
  $model = "";

  //$ret = $lv->domain_nic_add($domName, $mac, $network, $model) ? "Network has been successfully added to the guest" : "Cannot add network to the guest: ".$lv->get_last_error();


  //Return back to the orignal web page
  header('Location: ' . "guest-single.php?uuid=$uuid");
  exit;
}

require('navbar.php');
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
</script>


<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post">
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new network</h3>
            <h5 class="description">This form will allow you to add a new private network.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#network" data-toggle="tab"><i class="fas fa-database"></i>Storage</a>
                </li>
              </ul>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!--    Storage Tab     -->
              <div class="tab-pane fade" id="network">
                <h5 class="info-text"> Hard Drive Storage </h5>
                <div class="row justify-content-center">

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Network</label>
                      <select onchange="diskChangeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="network">
                        <?php
                        $networks = $lv->get_networks();
                        for ($i = 0; $i < sizeof($networks; $i++))
                          echo "<option value=\"$networks[$i]\"> $networks[$i] </option>";
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
                        <option value="qcow2" selected="selected">qcow2</option>
                        <option value="raw" >raw</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-10 diskChange" id="new" style="display:none;">
                    <div class="form-group">
                      <label>Target bus</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="new_target_bus">
                        <option value="virtio" selected="selected">virtio</option>
                        <option value="ide">ide</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5 diskChange" id="existing" style="display:none;">
                    <div class="form-group">
                      <label>Driver type</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="existing_driver_type">
                        <option value="qcow2" selected="selected">qcow2</option>
                        <option value="raw">raw</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5 diskChange" id="existing" style="display:none;">
                    <div class="form-group">
                      <label>Target bus</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" name="existing_target_bus">
                        <option value="virtio" selected="selected">virtio</option>
                        <option value="ide">ide</option>
                      </select>
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
            <div class="pull-left">
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
