<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}
?>

<body class="nav-sm">
  <div class="container body">
    <div class="main_container">
      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
          <div class="navbar nav_title" style="border: 0;">
            <a href="../index.php" class="site_title"><img src="../assets/img/squarelogo.png" width="50px"> <span style="font-size:35px;">open<font style="color:#FF8C00;">VM</font></span></a>
          </div>

          <div class="clearfix"></div>


          <br />   <br />

          <!-- sidebar menu -->
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
              <h3><?php echo $hn; ?></h3>
              <ul class="nav side-menu">
                <li><a><i class="fa fa-home"></i> Host <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a href="host-info.php">Host Information</a></li>
                    <li><a href="host-storage.php">Host Storage Devices</a></li>
                    <li><a href="host-net.php">Host Network Devices</a></li>
                    <li><a href="host-pci.php">Host PCI Devices</a></li>
                    <li><a href="host-scsi.php">Host SCSI Devices</a></li>
                    <li><a href="host-usb.php">Host USB Devices</a></li>
                  </ul>
                </li>
                <li><a><i class="fa fa-list"></i> Domains <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a href="domain-list.php">Domain List</a></li>
                    <li><a href="domain-wizard.php">Create new domain</a></li>
                  </ul>
                </li>
                <li><a><i class="fa fa-database"></i> Storage <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a href="storage-pools.php">Storage Pools</a></li>
                    <li><a href="storage-wizard-pools.php">Create new storage pool</a></li>
                    <li><a href="storage-upload-iso.php">Upload new ISO image</a></li>
                  </ul>
                </li>
                <li><a><i class="fa fa-sitemap"></i> Networking <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a href="network-list.php">Virtual Networks</a></li>
                    <li><a href="network-wizard.php">Create new network</a></li>
                  </ul>
                </li>

              </ul>
            </div>
          </div>
          <!-- /sidebar menu -->

          <!-- /menu footer buttons -->
          <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Domains" href="domain-list.php">
              <span class="fa fa-list" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Storage" href="storage-pools.php">
              <span class="fa fa-database" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Networking" href="network-list.php">
              <span class="fa fa-sitemap" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="../index.php?action=logout">
              <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
          </div>
          <!-- /menu footer buttons -->
        </div>
      </div>

      <!-- top navigation -->
      <div class="top_nav">
        <div class="nav_menu">
          <nav>
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <img src="../images/img.jpg" alt=""><?php echo $_SESSION['username']; ?>
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="../index.php?action=logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  <li><a href="user-password-reset.php"><i class="fa fa-key pull-right"></i> Reset Password</a></li>
                </ul>
              </li>

            </ul>
          </nav>
        </div>
      </div>
      <!-- /top navigation -->
