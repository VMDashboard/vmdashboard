<?php

//shell_exec("./noVNC/utils/websockify/run --web /var/www/html/openVM/noVNC/ --target-config ./tokens.list 192.168.2.10:6080 >/dev/null 2>&1 &");
$fileDir = dirname(__FILE__);
shell_exec("./apps/noVNC/utils/websockify/run --web $fileDir/apps/noVNC/ --target-config ./tokens.list 6080 > logs/novnc.log 2>&1 &");



header('Location: pages/domain-list.php');
 ?>
