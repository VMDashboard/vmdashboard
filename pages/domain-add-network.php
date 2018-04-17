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
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $mac = $_POST['mac'];
  $network = $_POST['network'];
  $model = $_POST['model'];

  $ret = $lv->domain_nic_add($domName, $mac, $network, $model) ? "success" : "Cannot add network to the guest: ".$lv->get_last_error();

  if ($ret == "success"){
  //Return back to the orignal web page
  header('Location: ' . "domain-single.php?uuid=$uuid");
  exit;
  }
}

require('navigation.php');
?>

<?php
if ($ret) {
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


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Network Wizard</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add new network</h2>
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
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post">
              <div class="form-horizontal form-label-left" style="min-height: 250px;">

                <div class="form-group">
                  <label for="network" class="control-label col-md-3 col-sm-3 col-xs-12">Network</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select class="form-control" name="network">
                      <?php
                      $networks = $lv->get_networks();
                      for ($i = 0; $i < sizeof($networks); $i++) {
                        echo "<option value=\"$networks[$i]\"> $networks[$i] </option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="model" class="control-label col-md-3 col-sm-3 col-xs-12">Model</label>
                  <div class="col-md-9 col-sm-9 col-xs-12">
                    <select class="form-control" name="model">
                      <?php
                      $models = $lv->get_nic_models();
                      for ($i = 0; $i < sizeof($models); $i++) {
                        echo "<option value=\"$models[$i]\"> $models[$i] </option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mac">MAC Address <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php $random_mac = $lv->generate_random_mac_addr();?>
                    <input type="text" value="<?php echo $random_mac; ?>" required="required" id="DataImageName" placeholder="Enter MAC Address" class="form-control" name="mac" />
                  </div>
                </div>

              </div>

              <div class="actionBar">
                <input type="submit" name="submit" class="buttonFinish btn btn-default" value="Finish" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->


<?php
require('footer.php');
?>
