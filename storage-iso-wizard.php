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





                        <!-- Bootstrap styles -->
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                        <!-- Generic page styles -->
                        <link rel="stylesheet" href="jQuery-File-Upload/css/style.css">
                        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
                        <link rel="stylesheet" href="jQuery-File-Upload/css/jquery.fileupload.css">


                        <div class="container">

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

                            </div>
                        </div>
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                        <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
                        <script src="jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
                        <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
                        <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
                        <!-- The Canvas to Blob plugin is included for image resizing functionality -->
                        <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
                        <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                        <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
                        <script src="jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
                        <!-- The basic File Upload plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload.js"></script>
                        <!-- The File Upload processing plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
                        <!-- The File Upload image preview & resize plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload-image.js"></script>
                        <!-- The File Upload audio preview plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload-audio.js"></script>
                        <!-- The File Upload video preview plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload-video.js"></script>
                        <!-- The File Upload validation plugin -->
                        <script src="jQuery-File-Upload/js/jquery.fileupload-validate.js"></script>
                        <script>
                        /*jslint unparam: true, regexp: true */
                        /*global window, $ */
                        $(function () {
                            'use strict';
                            // Change this to the location of your server-side upload handler:
                            var url = 'uploads/',
                                uploadButton = $('<button/>')
                                    .addClass('btn btn-primary')
                                    .prop('disabled', true)
                                    .text('Processing...')
                                    .on('click', function () {
                                        var $this = $(this),
                                            data = $this.data();
                                        $this
                                            .off('click')
                                            .text('Abort')
                                            .on('click', function () {
                                                $this.remove();
                                                data.abort();
                                            });
                                        data.submit().always(function () {
                                            $this.remove();
                                        });
                                    });
                            $('#fileupload').fileupload({
                                url: url,
                                dataType: 'json',
                                autoUpload: true,
                                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|iso)$/i,
                                maxFileSize: 999000000,
                                maxChunkSize: 1000000 // 1 MB

                            }).on('fileuploadadd', function (e, data) {
                                data.context = $('<div/>').appendTo('#files');
                                $.each(data.files, function (index, file) {
                                    var node = $('<p/>')
                                            .append($('<span/>').text(file.name));
                                    if (!index) {
                                        node
                                            .append('<br>')
                                            .append(uploadButton.clone(true).data(data));
                                    }
                                    node.appendTo(data.context);
                                });
                            }).on('fileuploadprocessalways', function (e, data) {
                                var index = data.index,
                                    file = data.files[index],
                                    node = $(data.context.children()[index]);
                                if (file.preview) {
                                    node
                                        .prepend('<br>')
                                        .prepend(file.preview);
                                }
                                if (file.error) {
                                    node
                                        .append('<br>')
                                        .append($('<span class="text-danger"/>').text(file.error));
                                }
                                if (index + 1 === data.files.length) {
                                    data.context.find('button')
                                        .text('Upload')
                                        .prop('disabled', !!data.files.error);
                                }
                            }).on('fileuploadprogressall', function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10);
                                $('#progress .progress-bar').css(
                                    'width',
                                    progress + '%'
                                );
                            }).on('fileuploaddone', function (e, data) {
                                $.each(data.result.files, function (index, file) {
                                    if (file.url) {
                                        var link = $('<a>')
                                            .attr('target', '_blank')
                                            .prop('href', file.url);
                                        $(data.context.children()[index])
                                            .wrap(link);
                                    } else if (file.error) {
                                        var error = $('<span class="text-danger"/>').text(file.error);
                                        $(data.context.children()[index])
                                            .append('<br>')
                                            .append(error);
                                    }
                                });
                            }).on('fileuploadfail', function (e, data) {
                                $.each(data.files, function (index) {
                                    var error = $('<span class="text-danger"/>').text('File upload failed.');
                                    $(data.context.children()[index])
                                        .append('<br>')
                                        .append(error);
                                });
                            }).prop('disabled', !$.support.fileInput)
                                .parent().addClass($.support.fileInput ? undefined : 'disabled');
                        });
                        </script>









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
</div>

<?php
require('footer.php');
?>
