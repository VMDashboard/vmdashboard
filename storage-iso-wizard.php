<?php
require('header.php');
?>

<?php
$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
//will redirect to guests.php. header() needs to be before navbar.php. Uses libvirst so has to be after header.php
if (isset($_POST['finish'])) {
  $pool = $_POST['pool'];
  //$volume_image_name = $_POST['volume_image_name'];
  //Just in case someone entered in a volume name with spaces, lets remove them
  //$volume_image_name = str_replace(' ','',$volume_image_name);
  //$volume_capacity = $_POST['volume_size'];
  //$volume_size = $_POST['volume_size'];
  //$unit = $_POST['unit'];
  //$driver_type = $_POST['driver_type'];
  $original_page = $_POST['original_page'];



  //Location where uploaded images go
  $target_dir = "/var/lib/libvirt/images";
  //Filepath for uploaded image
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  //Determine the file extension
  $target_file_extension = pathinfo($target_file,PATHINFO_EXTENSION);
  //Check for file type
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $file_type = finfo_file($finfo, $_FILES["fileToUpload"]["tmp_name"]);

  //Check for duplicate file names
  if (file_exists($target_file)){
  	$msg = "A file by that name already exists";
  }
  //Check file size restriction
  if ($_FILES["fileToUpload"]["size"] > 2000000){
  	$msg = "You file was too big";
  }
  //Time to do the uploading
  $ret = move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) ? "success" : "failed";



  if ($ret != "") {
  echo "
  <script>
  var alertRet = '$ret $file_type'
  swal(alertRet);
  </script>";
  }



  //header('Location: ' . $original_page);
  //exit;
}





require('navbar.php');


if ($ret != "") {
echo "
<script>
var alertRet = '$ret $file_type'
swal(alertRet);
</script>";
}



?>

<script>
function newExtenstion(f) {
  var diskName = f.volume_image_name.value;
  diskName = diskName.replace(/\s+/g, '');
  var n = diskName.lastIndexOf(".");
  var noExt = n > -1 ? diskName.substr(0, n) : diskName;
  var driverType = f.driver_type.value;
  if (driverType === "qcow2"){
    var ext = ".qcow2";
    var fullDiskName = noExt.concat(ext);
    f.volume_image_name.value = fullDiskName;
  }
  if (driverType === "raw"){
    var ext = ".img";
    var fullDiskName = noExt.concat(ext);
    f.volume_image_name.value = fullDiskName;
  }
}
</script>


<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post" enctype="multipart/form-data">
        <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->

          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new iso image</h3>
            <h5 class="description">This form will allow you to upload a new iso image.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab">
                    <i class="fas fa-database"></i>
                        ISO Image Upload
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
                      <label>Select ISO file to upload</label>
                      <span class="btn btn-raised btn-round btn-default btn-file">
	                       <span class="fileinput-new">Select ISO</span>
	                       <span class="fileinput-exists">Change</span>
                         <input type="file" name="fileToUpload" id="fileToUpload" data-style="btn btn-plain btn-round"/>
                      </span>
                    </div>
                  </div>


                  <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" name="original_page"/>
                  <input type="hidden" value="<?php echo $_GET['pool']; ?>" name="pool"/>

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
