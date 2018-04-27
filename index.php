<?php
//create an initial setup page. Have it configure database, username, password,
//default storage pool and default network (may already be created by default).
//initial setup page will create config.php after complete



//shell_exec("./noVNC/utils/websockify/run --web /var/www/html/openVM/noVNC/ --target-config ./tokens.list 192.168.2.10:6080 >/dev/null 2>&1 &");
$fileDir = dirname(__FILE__);
shell_exec("./apps/noVNC/utils/websockify/run --web $fileDir/apps/noVNC/ --target-config ./tokens.list 6080 > logs/novnc.log 2>&1 &");

session_start();

if($_SESSION['username'])
  header('Location: pages/domain-list.php');

$path = dirname(__FILE__) . "/config.php";
echo $path;
if (file_exists($path)) {
  header('Location: pages/login.php');
} else {
header('Location: pages/setup.php');
}

 ?>
