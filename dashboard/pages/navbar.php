<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        <a href="#" class="simple-text logo-mini">
          <div class="logo-image-small">
            <img src="../../assets/img/squarelogo.png">
          </div>
        </a>
        <a href="#" class="simple-text logo-normal">
          OPENVM.TECH
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
                  <a href="#">
                    <span class="sidebar-mini-icon">LO</span>
                    <span class="sidebar-normal">LogOut</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span class="sidebar-mini-icon">CP</span>
                    <span class="sidebar-normal">Change Password</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <ul class="nav">
          <?php echo (basename($_SERVER['PHP_SELF']) == "domains.php") ? '<li class="active">' : '<li>'; ?>
            <a href="../domain/domain-list.php">
              <i class="nc-icon nc-laptop"></i>
              <p>Home</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php" || basename($_SERVER['PHP_SELF']) == "domain-single.php" || basename($_SERVER['PHP_SELF']) == "domain-create.php" || basename($_SERVER['PHP_SELF']) == "domain-add-volume.php" || basename($_SERVER['PHP_SELF']) == "domain-add-iso.php" || basename($_SERVER['PHP_SELF']) == "domain-add-network.php") ? '<li class="active">' : '<li>'; ?>
            <a data-toggle="collapse" href="#pagesDomains">
              <i class="nc-icon nc-bullet-list"></i>
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
                    <span class="sidebar-normal"> List VMs</span>
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
              <li class="nav-item">
                <a class="nav-link btn-rotate" href="#">
                  <i class="nc-icon nc-settings-gear-65"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
