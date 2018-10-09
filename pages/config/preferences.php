<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}

// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['cert_path'] = $_POST['cert_path'];
    $_SESSION['themeColor'] = $_POST['theme_color'];
    $_SESSION['themeColorChange'] = $_POST['theme_color'];
    $_SESSION['language'] = $_POST['language'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

require('config.php');
// Creating table if necessary to store setttings
$sql = "CREATE TABLE IF NOT EXISTS vmdashboard_config ( name VARCHAR(255), value VARCHAR(255), userid int );";
$result = $conn->query($sql);

if (isset($_SESSION['cert_path'])) {
  //Capturing the POST Data
  $cert_path = $_SESSION['cert_path'];

  $sql = "SELECT name FROM vmdashboard_config WHERE name = 'cert_path';";
  $result = $conn->query($sql);

  if (mysqli_num_rows($result) == 0 ) {
    $sql = "INSERT INTO vmdashboard_config (name, value) VALUES ('cert_path', '$cert_path');";
    $result = $conn->query($sql);
  } else {
    $sql = "UPDATE vmdashboard_config SET value = '$cert_path' WHERE name = 'cert_path';";
    $result = $conn->query($sql);
  }

  unset($_SESSION['ssl_path']);
} //End if statement for POST data check


if (isset($_SESSION['themeColorChange'])) {
  //Capturing the POST Data
  $themeColorChange = $_SESSION['themeColorChange'];
  $userid = $_SESSION['userid'];

  $sql = "SELECT name FROM vmdashboard_config WHERE name = 'theme_color' AND userid = '$userid';";
  $result = $conn->query($sql);

  if (mysqli_num_rows($result) == 0 ) {
    $sql = "INSERT INTO vmdashboard_config (name, value, userid) VALUES ('theme_color', '$themeColorChange', '$userid');";
    $result = $conn->query($sql);
  } else {
    $sql = "UPDATE vmdashboard_config SET value = '$themeColorChange' WHERE name = 'theme_color' AND userid = '$userid';";
    $result = $conn->query($sql);
  }

  unset($_SESSION['themeColorChange']);
} //End if statement for POST data check


if (isset($_SESSION['language'])) {
  //Capturing the POST Data
  $language = $_SESSION['language'];
  $userid = $_SESSION['userid'];

  $sql = "SELECT name FROM vmdashboard_config WHERE name = 'language' AND userid = '$userid';";
  $result = $conn->query($sql);

  if (mysqli_num_rows($result) == 0 ) {
    $sql = "INSERT INTO vmdashboard_config (name, value, userid) VALUES ('language', '$language', '$userid');";
    $result = $conn->query($sql);
  } else {
    $sql = "UPDATE vmdashboard_config SET value = '$language' WHERE name = 'language' AND userid = '$userid';";
    $result = $conn->query($sql);
  }

  //unset($_SESSION['language']);
} //End if statement for POST data check


//Get the current noVNC cert path to use as placeholder for textbox
$sql = "SELECT value FROM vmdashboard_config WHERE name = 'cert_path' LIMIT 1;";
$result = $conn->query($sql);
if (mysqli_num_rows($result) != 0 ) {
  while ($row = $result->fetch_assoc()) {
    $cert_path = $row['value'];
  }
} else {
  $cert_path = "/etc/ssl/self.pem";
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
            <h3 class="card-title">User Preferences</h3>
            <p class="card-category">User: <?php echo $_SESSION['username']; ?></p>
          </div>
          <div class="card-body">
            <br />

            <div class="row">
              <label class="col-5 col-form-label">Language: </label>
              <div class="col-4">
                <div class="form-group">
                  <select class="form-control" name="language">
                    <option value="english" <?php if ($_SESSION['language'] == "english") { echo "selected"; } ?> >English (English)</option>
                <!--    <option value="spanish" <?php if ($_SESSION['language'] == "spanish") { echo "selected"; } ?> >Spanish (Español)</option> -->
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <label class="col-5 col-form-label">Theme: </label>
              <div class="col-4 checkbox-radios">
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="theme_color" value="white" <?php if ($_SESSION['themeColor'] != "dark-edition") {echo "checked";} ?> > Standard
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="theme_color" value="dark-edition" <?php if ($_SESSION['themeColor'] == "dark-edition") {echo "checked";} ?> > Dark
                    <span class="circle">
                      <span class="check"></span>
                    </span>
                  </label>
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
