<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

$path = dirname(__FILE__) . "/pages/config/config.php";

//create an initial setup page. Have it configure database, username, password,
//default storage pool and default network (may already be created by default).
//initial setup page will create config.php after complete


//shell_exec("./noVNC/utils/websockify/run --web /var/www/html/openVM/noVNC/ --target-config ./tokens.list 192.168.2.10:6080 >/dev/null 2>&1 &");
$fileDir = dirname(__FILE__);

shell_exec("./apps/noVNC/utils/websockify/run --web $fileDir/apps/noVNC/ --target-config ./tokens.php 6080 > logs/novnc.log 2>&1 &");

//currently using GET to at the index page to indicate logout, may switch to SESSION VARIABLE
$action = $_GET['action'];
if ($action == "logout") {
  unset($_SESSION['username']);
}


//Redirect based on login session or initial setup complete
if (isset($_SESSION['username'])) {
  require('pages/libvirt.php');
  $lv = new Libvirt();
  if ($lv->connect("qemu:///system") == false)
    die('<html><body>Cannot open connection to hypervisor</body></html>');
  $pools = $lv->get_storagepools();
  if (empty($pools)) {
    header('Location: pages/storage/storage-pools.php');
  } else {
    header('Location: pages/domain/domain-list.php');
  }
} elseif (file_exists($path)) {
  header('Location: pages/login.php');
} else {
header('Location: pages/config/setup-database.php');
}

?>
