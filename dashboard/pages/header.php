<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/sqaurelogo.png">
    <link rel="icon" type="image/png" href="../../assets/img/squarelogo.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      openVM
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  </head>

  <?php
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
