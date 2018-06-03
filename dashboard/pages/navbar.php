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
          openVM
          <!-- <div class="logo-image-big">
            <img src="../../assets/img/logo-big.png">
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
              <i class="nc-icon nc-bank"></i>
              <p>Virtual Machines</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php") ? '<li class="active">' : '<li>'; ?>
            <a data-toggle="collapse" href="#pagesExamples">
              <i class="nc-icon nc-book-bookmark"></i>
              <p>
                Domains
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse " id="pagesExamples">
              <ul class="nav">
                <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php") ? '<li class="active">' : '<li>'; ?>
                  <a href="../domain/domain-list.php">
                    <span class="sidebar-mini-icon">VM</span>
                    <span class="sidebar-normal"> Virtual Machines </span>
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
