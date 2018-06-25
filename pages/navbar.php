<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        <a href="../../index.php" class="simple-text logo-mini">
          <div class="logo-image-small">
            <img src="../../assets/img/squarelogo.png">
          </div>
        </a>
        <a href="../../index.php" class="simple-text logo-normal">
          OPENVM
          <!--<div class="logo-image-big">
            <img src="../../assets/img/logo.png">
          </div> -->
        </a>
      </div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            <img src="../../assets/img/faces/defaultProfile.png" />
          </div>
          <div class="info">
            <a data-toggle="collapse" href="#collapseExample" class="collapsed">
              <span>
                <?php echo htmlentities($_SESSION['username']); ?>
                <b class="caret"></b>
              </span>
            </a>
            <div class="clearfix"></div>
            <div class="collapse" id="collapseExample">
              <ul class="nav">
                <li>
                  <a href="../../index.php?action=logout">
                    <span class="sidebar-mini-icon">LO</span>
                    <span class="sidebar-normal">LogOut</span>
                  </a>
                </li>
                <li>
                  <a href="../config/setup-password-change.php">
                    <span class="sidebar-mini-icon">CP</span>
                    <span class="sidebar-normal">Change Password</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <ul class="nav">
          <?php echo (basename($_SERVER['PHP_SELF']) == "host-info.php") ? '<li class="active">' : '<li>'; ?>
            <a href="../host/host-info.php">
              <i class="nc-icon nc-laptop"></i>
              <p>Host</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php" || basename($_SERVER['PHP_SELF']) == "domain-single.php" || basename($_SERVER['PHP_SELF']) == "domain-create.php" || basename($_SERVER['PHP_SELF']) == "domain-add-volume.php" || basename($_SERVER['PHP_SELF']) == "domain-add-iso.php" || basename($_SERVER['PHP_SELF']) == "domain-add-network.php") ? '<li class="active">' : '<li>'; ?>
            <a data-toggle="collapse" href="#pagesDomains">
              <i class="nc-icon nc-bullet-list-67"></i>
              <p>
                Virtual Machines
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php" || basename($_SERVER['PHP_SELF']) == "domain-single.php" || basename($_SERVER['PHP_SELF']) == "domain-create.php" || basename($_SERVER['PHP_SELF']) == "domain-add-volume.php" || basename($_SERVER['PHP_SELF']) == "domain-add-iso.php" || basename($_SERVER['PHP_SELF']) == "domain-add-network.php") ? 'show' : ''; ?>" id="pagesDomains">
              <ul class="nav">
                <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../domain/domain-list.php">
                    <span class="sidebar-mini-icon">LV</span>
                    <span class="sidebar-normal"> VM List</span>
                  </a>
                </li>
                <?php echo (basename($_SERVER['PHP_SELF']) == "domain-create.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../domain/domain-create.php">
                    <span class="sidebar-mini-icon">CV</span>
                    <span class="sidebar-normal"> Create New VM</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "storage-pools.php" || basename($_SERVER['PHP_SELF']) == "storage-add-pool.php") ? '<li class="active">' : '<li>'; ?>
            <a data-toggle="collapse" href="#pagesStorage">
              <i class="nc-icon nc-box"></i>
              <p>
                Storage
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse <?php echo (basename($_SERVER['PHP_SELF']) == "storage-pools.php" || basename($_SERVER['PHP_SELF']) == "storage-add-pool.php") ? 'show' : ''; ?>" id="pagesStorage">
              <ul class="nav">
                <?php echo (basename($_SERVER['PHP_SELF']) == "storage-pools.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../storage/storage-pools.php">
                    <span class="sidebar-mini-icon">SP</span>
                    <span class="sidebar-normal"> Storage Pools </span>
                  </a>
                </li>
                <?php echo (basename($_SERVER['PHP_SELF']) == "storage-add-pool.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../storage/storage-add-pool.php">
                    <span class="sidebar-mini-icon">CP</span>
                    <span class="sidebar-normal"> Create New Pool </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "network-list.php" || basename($_SERVER['PHP_SELF']) == "network-add-lan.php") ? '<li class="active">' : '<li>'; ?>
            <a data-toggle="collapse" href="#pagesNetwork">
              <i class="nc-icon nc-vector"></i>
              <p>
                Network
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse <?php echo (basename($_SERVER['PHP_SELF']) == "network-list.php" || basename($_SERVER['PHP_SELF']) == "network-add-lan.php") ? 'show' : ''; ?>" id="pagesNetwork">
              <ul class="nav">
                <?php echo (basename($_SERVER['PHP_SELF']) == "network-list.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../network/network-list.php">
                    <span class="sidebar-mini-icon">NL</span>
                    <span class="sidebar-normal"> Network List </span>
                  </a>
                </li>
                <?php echo (basename($_SERVER['PHP_SELF']) == "network-add-lan.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../network/network-add-lan.php">
                    <span class="sidebar-mini-icon">CN</span>
                    <span class="sidebar-normal"> Create New Network </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>



        </ul>
      </div>
    </div>

    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-icon btn-round">
                <i class="nc-icon nc-minimal-right text-center visible-on-sidebar-mini"></i>
                <i class="nc-icon nc-minimal-left text-center visible-on-sidebar-regular"></i>
              </button>
            </div>
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="#">Dashboard</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">


<?php
/*
$arrayLatest = file('https://raw.githubusercontent.com/PenningDevelopment/openVM/master/pages/config/version.php');
$arrayCurrent = file('../config/version.php');
if ($arrayLatest[0] > $arrayCurrent[0])
  $notification_status = true;

if ($notification_status == true) { ?>
              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle"  id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="nc-icon nc-bell-55"></i>
                  <span class="notification">1</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item">New Update Available</a>
                </div>
              </li>
<?php } */?>

              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownSettingsLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="nc-icon nc-settings-gear-65"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownSettingsLink">
                  <a class="dropdown-item" href="../config/update.php">Update</a>
                  <a class="dropdown-item" href="../../index.php?action=logout">Logout</a>
                </div>
              </li>

            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
