<?php
require('header.php');
?>

<?php
$uuid = $_GET['uuid'];
$domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
//will redirect to guests.php. header() needs to be before navbar.php. Uses libvirst so has to be after header.php
if (isset($_POST['finish'])) {
  $pool = $_POST['pool'];
  $volume_image_name = $_POST['volume_image_name'];
  $volume_capacity = $_POST['volume_size'];
  $volume_size = $_POST['volume_size'];
  $unit = $_POST['unit'];
  $driver_type = $_POST['driver_type'];
  $original_page = $_POST['original_page'];

  $ret = $lv->domain_disk_add($domName, $source_file, $target_dev, $target_bus, $driver_type) ? "Disk has been successfully added to the guest" : "Cannot add disk to the guest: ".$lv->get_last_error();
  $msg = $lv->storagevolume_create($pool, $volume_image_name, $volume_capacity.$unit, $volume_size.$unit, $driver_type) ? 'Volume has been created successfully' : 'Cannot create volume';
  header('Location: ' . $original_page);
  exit;
}


require('navbar.php');
?>



<div class="panel-header panel-header-sm"></div>
<div class="content">
  <div class="col-md-10 mr-auto ml-auto">
    <!--      Wizard container        -->
    <div class="wizard-container">
      <div class="card card-wizard" data-color="primary" id="wizardProfile">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?uuid=' . $uuid;?>" method="post">
        <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->

          <div class="card-header text-center" data-background-color="orange">
            <h3 class="card-title">Add new disk volume image</h3>
            <h5 class="description">This form will allow you to create a new disk volume image.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab">
                    <i class="now-ui-icons files_box"></i>
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
                      <label>Volume image name</label>
                      <input type="text" placeholder="Ex. volumename.qcow2 or volumename.img" class="form-control" name="volume_image_name" />
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Volume size</label>
                      <input type="number" class="form-control" name="volume_size" />
                    </div>
                  </div>

                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Unit size</label>
                      <select class="selectpicker" data-style="btn btn-primary btn-round" title="Select Unit Size" name="unit">
                        <option value="M">MB</option>
                        <option value="G" selected>GB</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Driver type</label>
                      <select class="selectpicker" data-style="btn btn-primary btn-round" title="Select volume type" name="driver_type">
                        <option value="qcow2" selected>qcow2</option>
                        <option value="raw">raw</option>
                      </select>
                    </div>
                  </div>




                  <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" name="original_page"/>
                  <input type="hidden" value="<?php echo $_GET['pool']; ?>" name="pool"/>

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

<footer class="footer" >
  <div class="container-fluid">
    <nav>
            <ul>
                <li>
                    <a href="https://www.creative-tim.com">
                        Creative Tim
                    </a>
                </li>
                <li>
                    <a href="http://presentation.creative-tim.com">
                       About Us
                    </a>
                </li>
                <li>
                    <a href="http://blog.creative-tim.com">
                       Blog
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright">
            &copy; <script>document.write(new Date().getFullYear())</script>, Designed by <a href="https://www.invisionapp.com" target="_blank">Invision</a>. Coded by <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a>.
        </div>
    </div>
</footer>


          </div>

        </div>

    </body>

    <!--   Core JS Files   -->
<script src="assets/js/core/jquery.min.js" ></script>
<script src="assets/js/core/popper.min.js" ></script>
<script src="assets/js/core/bootstrap.min.js" ></script>
<script src="assets/js/plugins/perfect-scrollbar.jquery.min.js" ></script>

<script src="assets/js/plugins/moment.min.js"></script>



<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="assets/js/plugins/bootstrap-switch.js"></script>

<!--  Plugin for Sweet Alert -->
<script src="assets/js/plugins/sweetalert2.min.js"></script>

<!-- Forms Validations Plugin -->
<script src="assets/js/plugins/jquery.validate.min.js"></script>

<!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<script src="assets/js/plugins/jquery.bootstrap-wizard.js"></script>

<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="assets/js/plugins/bootstrap-selectpicker.js" ></script>

<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="assets/js/plugins/bootstrap-datetimepicker.js"></script>

<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
<script src="assets/js/plugins/jquery.dataTables.min.js"></script>

<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="assets/js/plugins/bootstrap-tagsinput.js"></script>

<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="assets/js/plugins/jasny-bootstrap.min.js"></script>

<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
<script src="assets/js/plugins/fullcalendar.min.js"></script>

<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
<script src="assets/js/plugins/jquery-jvectormap.js"></script>

<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="assets/js/plugins/nouislider.min.js" ></script>


<!--  Google Maps Plugin    -->

<script  src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>


<!-- Chart JS -->
<script src="assets/js/plugins/chartjs.min.js"></script>

<!--  Notifications Plugin    -->
<script src="assets/js/plugins/bootstrap-notify.js"></script>

<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->

<script src="assets/js/now-ui-dashboard.js?v=1.0.1" ></script>


<!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
<script src="assets/demo/demo.js"></script>



  <script>
  $(document).ready(function(){
    // Initialise the wizard
    demo.initNowUiWizard();
    setTimeout(function(){
      $('.card.card-wizard').addClass('active');
    },600);
  });
</script>




</html>
