<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

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

    $notification = $lv->storagepool_define_xml($xml) ? "" : "Cannot add storagepool: ".$lv->get_last_error();

    //Return back to the storage-pools page if successful
    if (!$notification){
      header('Location: storage-pools.php');
      exit;
    }

  } else {
    unset($_SESSION['pool_name']);
  }

} //end if $_SESSION

require('../navbar.php');

?>

<div class="content">

  <div class="row">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">storage</i>
            </div>
            <h3 class="card-title">Register Storage Pool</h3>
            <p class="card-category">Host: <?php echo $hn; ?> </p>
          </div>
          <div class="card-body">
            <br />

            <div class="row">
              <label class="col-3 col-form-label">Pool Name: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="default" required="required" placeholder="Enter name for storage pool" class="form-control" name="pool_name" />
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">Pool Path: </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="/var/lib/libvirt/images" required="required" placeholder="Enter full filepath" class="form-control" name="pool_path" />
                  <br /> * Only paths that start with <em>/var/lib/libvirt</em>, <em>/media</em>, or <em>/mnt</em> will be allowed
                </div>
              </div>
            </div>

          </div> <!-- end card body -->
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-warning">Submit</button>
          </div>
        </form>
      </div> <!-- end card -->
    </div>
  </div> <!-- end row -->
</div> <!-- end content -->

<script>
window.onload =  function() {
  <?php
  if ($notification) {
    echo "showNotification(\"top\",\"right\",\"$notification\");";
  }
  ?>
}

function showNotification(from, align, text){
    color = 'warning';
    $.notify({
        icon: "",
        message: text
      },{
          type: color,
          timer: 500,
          placement: {
              from: from,
              align: align
          }
      });
}
</script>

<?php
require('../footer.php');
?>
