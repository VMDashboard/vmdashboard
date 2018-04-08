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

  //header('Location: ' . $original_page);
  //exit;
}


require('navbar.php');

//if ($ret != "") {
echo "
<script>
var alertRet = '$ret $file_type'
swal(alertRet);
</script>";
//}



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


                  <div class="container">
                      <h1>jQuery File Upload Demo</h1>
                      <h2 class="lead">Basic Plus version</h2>


                      <br>
                      <!-- The fileinput-button span is used to style the file input field as button -->
                      <span class="btn btn-success fileinput-button">
                          <i class="glyphicon glyphicon-plus"></i>
                          <span>Add files...</span>
                          <!-- The file input field used as target for the file upload widget -->
                          <input id="fileupload" type="file" name="files[]" multiple>
                      </span>
                      <br>
                      <br>
                      <!-- The global progress bar -->
                      <div id="progress" class="progress">
                          <div class="progress-bar progress-bar-success"></div>
                      </div>
                      <!-- The container for the uploaded files -->
                      <div id="files" class="files"></div>
                      <br>
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h3 class="panel-title">Demo Notes</h3>
                          </div>
                          <div class="panel-body">
                              <ul>
                                  <li>The maximum file size for uploads in this demo is <strong>999 KB</strong> (default file size is unlimited).</li>
                                  <li>Only image files (<strong>JPG, GIF, PNG</strong>) are allowed in this demo (by default there is no file type restriction).</li>
                                  <li>Uploaded files will be deleted automatically after <strong>5 minutes or less</strong> (demo files are stored in memory).</li>
                                  <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage (see <a href="https://github.com/blueimp/jQuery-File-Upload/wiki/Browser-support">Browser support</a>).</li>
                                  <li>Please refer to the <a href="https://github.com/blueimp/jQuery-File-Upload">project website</a> and <a href="https://github.com/blueimp/jQuery-File-Upload/wiki">documentation</a> for more information.</li>
                                  <li>Built with the <a href="http://getbootstrap.com/">Bootstrap</a> CSS framework and Icons from <a href="http://glyphicons.com/">Glyphicons</a>.</li>
                              </ul>
                          </div>
                      </div>
                  </div>











                  <div class="col-sm-10">
                    <div class="form-group">
                      <div class="custom-file">

                  <input id="fileupload" type="file" name="files[]" data-url="server/php/" multiple>
                  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
                  <script src="jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
                  <script src="jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
                  <script src="jQuery-File-Upload/js/jquery.fileupload.js"></script>
                  <script>
                  $(function () {
                      $('#fileupload').fileupload({
                          dataType: 'json',
                          done: function (e, data) {
                              $.each(data.result.files, function (index, file) {
                                  $('<p/>').text(file.name).appendTo(document.body);
                              });
                          }
                      });
                  });
                  </script>
                  <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                  <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
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
