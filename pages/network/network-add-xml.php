<?php

// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

// We are now going to grab any GET/POST data and put in in SESSION data, then clear it.
// This will prevent duplicatig actions when page is reloaded.
if (isset($_POST['xml'])) {
  $_SESSION['xml'] = $_POST['xml'];
  $_SESSION['action'] = "NETWORK FROM XML";
  header("Location: ".$_SERVER['PHP_SELF']);
  exit;
}

require('../header.php');

if ($_SESSION['action'] == "NETWORK FROM XML") {
  $xml = $_SESSION['xml'];
  
  unset($_SESSION['xml']);
  unset($_SESSION['action']);

  $network_add = $lv->network_define_xml($xml);
 
  if (!$network_add){
    $notification = "Error defining network from XML: " . $lv->get_last_error();
    $notification = filter_var($notification,FILTER_SANITIZE_SPECIAL_CHARS); //Error message will contain special characters
  }
  
  //Return back to the domain-single page if successful
  if (!$notification){
    header('Location: network-list.php');
    exit;
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
              <i class="material-icons">device_hub</i>
            </div>
            <h3 class="card-title">Define Network from XML</h3>
            <p class="card-category">XML Description</p>
          </div>
          <div class="card-body">
            <br />
            <div class="row">
              <label class="col-3 col-form-label">XML Definition: </label>
              <div class="col-6">
                <div class="form-group">
                  <textarea name="xml" class="form-control" rows="10"></textarea>
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
window.onload = function() {
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
