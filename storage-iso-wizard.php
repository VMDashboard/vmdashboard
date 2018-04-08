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



                      <div class="container">
                          <h4>ISO File Upload</h4>
                          <br>
                          <!-- The fileinput-button span is used to style the file input field as button -->
                          <span class="btn btn-success fileinput-button">
                              <span>Select ISO file...</span>
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

                          </div>
                      </div>
















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
