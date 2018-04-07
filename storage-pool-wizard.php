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

$xml_default = "
<pool type='dir'>
  <name>defaulttest</name>
  <uuid>590c3fdc-a6bb-48c7-aad4-aa39802fba8d</uuid>
  <capacity unit='bytes'>538628128768</capacity>
  <allocation unit='bytes'>124266442752</allocation>
  <available unit='bytes'>414361686016</available>
  <source>
  </source>
  <target>
    <path>/var/lib/libvirt/images</path>
    <permissions>
    <mode>0711</mode>
    <owner>0</owner>
    <group>0</group>
    </permissions>
  </target>
</pool>";

$xml = "
<pool type='dir'>
  <name>defaulttest</name>
  <target>
    <path>/tmp</path>
    <permissions>
    </permissions>
  </target>
</pool>";

}

$ret = $lv->storagepool_define_xml($xml);

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
            <h3 class="card-title">Add new storage pool</h3>
            <h5 class="description">This form will allow you to create a new storage pool.</h5>
            <div class="wizard-navigation">
              <ul>
                <li class="nav-item">
                  <a class="nav-link" href="#storage" data-toggle="tab">
                    <i class="fas fa-database"></i>
                        Storage <br>



                        <?php
                        $pools = $lv->get_storagepools();
                        var_dump($pools);
                        ?>
                        <br>
                        <br>

                        <?php
                        $info = $lv->get_storagepool_info("default");
                        ?>
                        <textarea>
                        <?php echo $info['xml']; ?>
                        </textarea>
                        <br>
                        <?php
                        $info = $lv->get_storagepool_info("defaulttest");
                        ?>
                        <textarea>
                        <?php echo $info['xml']; ?>
                        </textarea>



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
                      <input type="text" value="newVolume.qcow2" placeholder="Enter name for new volume image" class="form-control" name="volume_image_name" />
                    </div>
                  </div>


                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Unit size</label>
                      <select class="selectpicker" data-style="btn btn-plain btn-round" title="Select Unit Size" name="unit">
                        <option value="M">MB</option>
                        <option value="G" selected>GB</option>
                      </select>
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
