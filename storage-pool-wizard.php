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

$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
//will redirect to guests.php. header() needs to be before navbar.php. Uses libvirst so has to be after header.php
if (isset($_POST['finish'])) {

  $pool_name = clean_name_input($_POST['pool_name']);
  $pool_path = $_POST['pool_path'];

  $xml = "
    <pool type='dir'>
      <name>$pool_name</name>
      <target>
        <path>$pool_path</path>
        <permissions>
        </permissions>
      </target>
    </pool>";

  $ret = $lv->storagepool_define_xml($xml) ? "success" : "Cannot add network to the guest: ".$lv->get_last_error();

  if ($ret == "success"){
  //Return back to the orignal web page
  header('Location: ' . "storage.php?action=new-pool");
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


<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->

          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new storage pool</h3>
            <h5 class="description">This form will allow you to create a new storage pool.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab">
                    <i class="fas fa-database"></i>
                        Storage
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!--    Storage Tab     -->
              <div class="tab-pane fade" id="storage">
                <h5 class="info-text"> New Volume Image </h5>
                <div class="row justify-content-center">

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Pool name</label>
                      <input type="text" value="default" placeholder="Enter name for storage pool" class="form-control" name="pool_name" />
                    </div>
                  </div>

                  <div class="col-sm-10">
                    <div class="form-group">
                      <label>Pool path</label>
                      <input type="text" value="/var/lib/libvirt/images" placeholder="Enter full filepath" class="form-control" name="pool_path" />
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

<?php
require('footer.php');
?>
