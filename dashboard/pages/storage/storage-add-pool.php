<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
//if (!isset($_SESSION['username'])){
//  header('Location: login.php');
//}

function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data);
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['pool_name'])) {
  $_SESSION['pool_name'] = clean_input($_POST['pool_name']);
  $_SESSION['pool_path'] = $_POST['pool_path'];

  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if (isset($_SESSION['pool_name'])) {
  $pool_path = $_SESSION['pool_path'];
  unset($_SESSION['pool_path']);
  if (substr($pool_path, 0, 16) == "/var/lib/libvirt" || substr($pool_path, 0, 4) == "/mnt" || substr($pool_path, 0, 6) == "/media") {
    $pool_name = $_SESSION['pool_name'];
    unset($_SESSION['pool_name']);
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

    header('Location: storage-pools.php');
    exit;

  } else {
    unset($_SESSION['pool_name']);
  }

} //end if $_SESSION

require('../navbar.php');

?>

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Add storage pool to <?php echo $hn; ?></h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#storagePool" role="tab" data-toggle="tab">Storage Pool</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="storagePool">

                <div class="row">
                  <label class="col-sm-2 col-form-label">Pool Name: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" value="default" required="required" placeholder="Enter name for storage pool" class="form-control" name="pool_name" />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">Pool Path: </label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input type="text" value="/var/lib/libvirt/images" required="required" placeholder="Enter full filepath" class="form-control" name="pool_path" />
                      <br /> * Only paths that start with <em>/var/lib/libvirt</em>, <strong>/media</strong>, or <strong>/mnt</strong> will be allowed
                    </div>
                  </div>
                </div>

              </div> <!-- end tab pane -->
            </div> <!-- end tab content -->
          </div>

        </div>
      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-danger">Submit</button>
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
