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
  $mac = $_POST['mac'];
  $network = $_POST['network'];
  $model = $_POST['model'];

  $ret = $lv->domain_nic_add($domName, $mac, $network, $model) ? "success" : "Cannot add network to the guest: ".$lv->get_last_error();

  if ($ret == "success"){
  //Return back to the orignal web page
  header('Location: ' . "guest-single.php?uuid=$uuid");
  exit;
  }
}

require('navbar.php');
?>

<?php
if ($ret != "") {
?>
<script>
var alertRet = "<?php echo $ret; ?>";
swal(alertRet);
</script>
<?php
}
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
                        for ($i = 0; $i < sizeof($networks); $i++) {
                          echo "<option value=\"$networks[$i]\"> $networks[$i] </option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Model</label>
                      <select onchange="diskChangeOptions(this)" class="selectpicker" data-style="btn btn-plain btn-round" name="model">
                        <?php
                        $models = $lv->get_nic_models();
                        for ($i = 0; $i < sizeof($models); $i++) {
                          echo "<option value=\"$models[$i]\"> $models[$i] </option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-10 diskChange">
                    <div class="form-group">
                      <label>Mac Address</label>
                      <?php $random_mac = $lv->generate_random_mac_addr();?>
                      <input type="text" value="<?php echo $random_mac; ?>" placeholder="Enter Mac Address" class="form-control" name="mac"/>
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
