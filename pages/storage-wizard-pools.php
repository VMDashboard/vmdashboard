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
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
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
  $ret = $lv->storagepool_define_xml($xml) ? "success" : "Cannot add storagepool: ".$lv->get_last_error();

  if ($ret == "success"){
  //Return back to the orignal web page
  header('Location: ' . "storage-pools.php");
  exit;
  }
} //end if statement for $_POST data

require('navigation.php');
?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Pool Wizard</h3>
      </div>
    </div>

    <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Create new storage pool</h2>
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
              <div class="form-horizontal form-label-left" style="min-height: 250px;">

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pool_name">Pool name <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" value="default" required="required" placeholder="Enter name for storage pool" class="form-control col-md-7 col-xs-12" name="pool_name" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pool_path">Pool path<span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" value="/var/lib/libvirt/images" required="required" placeholder="Enter full filepath" class="form-control col-md-7 col-xs-12" name="pool_path" />
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
