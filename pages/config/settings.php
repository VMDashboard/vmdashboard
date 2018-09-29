<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

// This function is used to prevent any problems with user form input
function clean_input($data) {
  $data = trim($data); //remove spaces at the beginning and end of string
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(' ','',$data); //remove any spaces within the string
  $data = str_replace('--','',$data); //remove -- within the string
  $data = filter_var($data, FILTER_SANITIZE_STRING);
  return $data;
}

// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['cert_path'] = clean_input($_POST['cert_path']);
    $_SESSION['key_path'] = clean_input($_POST['key_path']);
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

require('config.php');
// Creating table if necessary to store setttings
$sql = "CREATE TABLE IF NOT EXISTS openvm_config ( name VARCHAR(255), value VARCHAR(255), userid int );";
$result = $conn->query($sql);

//If the SSL Cert File Path has been changed change it now
if (isset($_SESSION['cert_path'])) {
  $cert_path = $_SESSION['cert_path'];
  unset($_SESSION['cert_path']);

  $sql = "SELECT name FROM openvm_config WHERE name = 'cert_path';";
  $result = $conn->query($sql);

  if (mysqli_num_rows($result) == 0 ) {
    $sql = "INSERT INTO openvm_config (name, value) VALUES ('cert_path', '$cert_path');";
    $result = $conn->query($sql);
  } else {
    $sql = "UPDATE openvm_config SET value = '$cert_path' WHERE name = 'cert_path';";
    $result = $conn->query($sql);
  }
}

//If the SSL Key File Path has been changed change it now
if (isset($_SESSION['key_path'])) {
  $key_path = $_SESSION['key_path'];
  unset($_SESSION['key_path']);

  $sql = "SELECT name FROM openvm_config WHERE name = 'key_path';";
  $result = $conn->query($sql);

  if (mysqli_num_rows($result) == 0 ) {
    $sql = "INSERT INTO openvm_config (name, value) VALUES ('key_path', '$key_path');";
    $result = $conn->query($sql);
  } else {
    $sql = "UPDATE openvm_config SET value = '$key_path' WHERE name = 'key_path';";
    $result = $conn->query($sql);
  }
}


//Get the current noVNC cert path to use as placeholder for textbox
$sql = "SELECT value FROM openvm_config WHERE name = 'cert_path' LIMIT 1;";
$result = $conn->query($sql);
if (mysqli_num_rows($result) != 0 ) {
  while ($row = $result->fetch_assoc()) {
    $cert_path = $row['value'];
  }
} else {
  $cert_path = "/etc/ssl/self.pem";
}

//Get the current noVNC key path to use as placeholder for textbox
$sql = "SELECT value FROM openvm_config WHERE name = 'key_path' LIMIT 1;";
$result = $conn->query($sql);
if (mysqli_num_rows($result) != 0 ) {
  while ($row = $result->fetch_assoc()) {
    $key_path = $row['value'];
  }
} else {
  $key_path = "";
}

// Time to bring in the header and navigation
require('../header.php');
require('../navbar.php');

?>


<div class="content">
  <div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
      <div class="card card-stats-center">
        <form action="" method="POST">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">album</i>
            </div>
            <h3 class="card-title">Settings</h3>
            <p class="card-category">User: <?php echo $_SESSION['username']; ?></p>
          </div>
          <div class="card-body">
            <br />

            <div class="row">
              <label class="col-3 col-form-label">SSL Certificate File Path (VNC): </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="<?php echo $cert_path; ?>" class="form-control" name="cert_path" />
                  <br />
                  <p>*You will need to restart your server or kill the python process using port 6080 to apply the change. Default file path: /etc/ssl/self.pem</p>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-3 col-form-label">SSL Key File Path (VNC): </label>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" value="<?php echo $key_path; ?>" class="form-control" name="key_path" />
                  <br />
                  <p>*You will need to restart your server or kill the python process using port 6080 to apply the change. Default file path is not set</p>
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
  </div>
</div> <!-- end content -->

<?php
require('../footer.php');
?>
