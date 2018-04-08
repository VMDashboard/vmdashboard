<?php
require('header.php');

require('navbar.php');


?>




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




                      <div class="navbar navbar-default navbar-fixed-top">
                          <div class="container">
                              <div class="navbar-header">
                                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-fixed-top .navbar-collapse">
                                      <span class="icon-bar"></span>
                                      <span class="icon-bar"></span>
                                      <span class="icon-bar"></span>
                                  </button>
                                  <a class="navbar-brand" href="https://github.com/blueimp/jQuery-File-Upload">jQuery File Upload</a>
                              </div>
                              <div class="navbar-collapse collapse">
                                  <ul class="nav navbar-nav">
                                      <li><a href="https://github.com/blueimp/jQuery-File-Upload/tags">Download</a></li>
                                      <li><a href="https://github.com/blueimp/jQuery-File-Upload">Source Code</a></li>
                                      <li><a href="https://github.com/blueimp/jQuery-File-Upload/wiki">Documentation</a></li>
                                      <li><a href="https://blueimp.net">&copy; Sebastian Tschan</a></li>
                                  </ul>
                              </div>
                          </div>
                      </div>
                      <div class="container">
                          <h1>jQuery File Upload Demo</h1>
                          <h2 class="lead">Basic Plus version</h2>
                          <ul class="nav nav-tabs">
                              <li><a href="jQuery-File-Upload/basic.html">Basic</a></li>
                              <li class="active"><a href="jQuery-File-Upload/basic-plus.html">Basic Plus</a></li>
                              <li><a href="jQuery-File-Upload/index.html">Basic Plus UI</a></li>
                              <li><a href="jQuery-File-Upload/angularjs.html">AngularJS</a></li>
                              <li><a href="jQuery-File-Upload/jquery-ui.html">jQuery UI</a></li>
                          </ul>
                          <br>
                          <blockquote>
                              <p>File Upload widget with multiple file selection, drag&amp;drop support, progress bar, validation and preview images, audio and video for jQuery.<br>
                              Supports cross-domain, chunked and resumable file uploads and client-side image resizing.<br>
                              Works with any server-side platform (PHP, Python, Ruby on Rails, Java, Node.js, Go etc.) that supports standard HTML form file uploads.</p>
                          </blockquote>
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
                              autoUpload: false,
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
