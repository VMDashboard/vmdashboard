<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/sqaurelogo.png">
    <link rel="icon" type="image/png" href="../../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      VM Dashboard
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../assets/css/material-dashboard.css?v=2.0.1" rel="stylesheet" />
    <link href="../../assets/css/vm-dashboard.css" rel="stylesheet" />
  </head>

  <?php

  //Change name of tables if still using openvm
  require('../config/config.php');
  $sql = "select * from openvm_users;"; //check to see if openvm_users table exits
  $openvm_result = $conn->query($sql);
  //if openvm_users table exists and has any values, rename the tables to vmdashboard
  if (mysqli_num_rows($openvm_result) != 0 ) {
    $sql = "RENAME TABLE openvm_users TO vmdashboard_users";
    $rename_result = $conn->query($sql);
  }
  $sql = "select * from openvm_config;"; //check to see if openvm_users table exits
  $openvm_result = $conn->query($sql);
  //if openvm_users table exists and has any values, rename the tables to vmdashboard
  if (mysqli_num_rows($openvm_result) != 0 ) {
    $sql = "RENAME TABLE openvm_config TO vmdashboard_config";
    $rename_result = $conn->query($sql);
  }


  //bring in the language
  if ($_SESSION['language'] == "english") {
    require('../config/lang/english.php');
  } elseif ($_SESSION['language'] == "spanish") {
    require('../config/lang/spanish.php');
  } else {
    require('../config/lang/english.php');
  }

  //bring in the libvirt class and methods
  require('../libvirt.php');
  $lv = new Libvirt();

  //attempt to connect to system
  if ($lv->connect("qemu:///system") == false)
    die('<html><body>Cannot open connection to hypervisor</body></html>');

  //attempt to learn the server's hostname
  $hn = $lv->get_hostname();
  if ($hn == false)
    die('<html><body>Cannot get hostname</body></html>');
  ?>
