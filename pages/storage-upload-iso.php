<?php
include('header.php');
include('navigation.php');
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Form Upload </h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>ISO Image Uploader</h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a>
                  </li>
                  <li><a href="#">Settings 2</a>
                  </li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>


          <div class="x_content" style="min-height:300px;">
            <p>This upload form will divide a large ISO file into 2MB chunks as it uploads to the server.<br>
              This attempts to bypass common upload size limits. Uploads are located in the uploads/iso_uploads/ directory.</p>
            <br />
            <br />

          <!-- The fileinput-button span is used to style the file input field as button -->
          <span class="btn btn-plain fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Add files...</span>
            <!-- The file input field used as target for the file upload widget -->
            <input id="fileupload" type="file" name="files[]">
          </span>
          <br>
          <!-- The global progress bar -->
          <div id="progress" class="progress">
            <div class="progress-bar progress-bar-success"></div>
          </div>
          <!-- The container for the uploaded files -->
          <div id="files" class="files"></div>
          <br>
          <div class="panel panel-default"></div>
<br>
<br>
<?php
$directory = "../uploads/iso_uploads/"; //assigned directory for uploading ISO images
$files = glob($directory . "*.[iI][sS][oO]"); //check for iso or ISO extension
if ($files){
  echo "<h2>Existing ISO Images</h2>";
}
for ($i = 0; $i < sizeof($files); $i++) {
  $iso_name = basename($files[$i]); //strips off the relative filepath and returns just filename
echo "<div class=\"col-md-1 col-sm-2 col-xs-4\" style=\"text-align:center;\">
      <center>
        <img style=\"width: 75%; display: block;\" src=\"../assets/img/cddvd.png\" alt=\"image\" />
      </center>
        <div class=\"caption\">
          <p>$iso_name</p>
        </div>
      </div>";
}
?>


  </div>


        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->


<!-- footer content -->
<footer>
  <div class="pull-right">
    openVM - Open Source Virtualization Management Software by <a href="https://openVM.tech">openVM.tech</a>
  </div>
  <div class="clearfix"></div>
</footer>
<!-- /footer content -->
  </div>
</div>
