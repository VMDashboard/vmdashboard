<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="../production/images/favicon.ico" type="image/ico" />

    <title>openVM</title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    

    <!-- ISO Uploads -->
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="../apps/jQuery-File-Upload/css/jquery.fileupload.css">

  </head>

  <?php
  //bring in the libvirt class and methods
  require('libvirt.php');
  $lv = new Libvirt();

  //attempt to connect to system
  if ($lv->connect("qemu:///system") == false)
    die('<html><body>Cannot open connection to hypervisor</body></html>');

  //attempt to learn the server's hostname
  $hn = $lv->get_hostname();
  if ($hn == false)
    die('<html><body>Cannot get hostname</body></html>');
  ?>

  <!--  Plugin for Sweet Alert -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
