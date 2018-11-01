<?php
//if theme is dark change sidebar data-color
if (isset($_SESSION[themeColor])){
  $themeColor = $_SESSION['themeColor'];
} else {
  $themeColor = "";
}
?>

<body class="<?php echo $themeColor; ?>">
  <div class="wrapper ">
    <div class="sidebar" data-color="" data-background-color="black" data-image="../../assets/img/">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"
        Tip 2: you can also add an image using data-image tag
      -->
      <div class="logo">
        <a href="http://vmdashboard.org" class="simple-text logo-normal">
          <img src="../../assets/img/squarelogo.png" width="24px"> &ensp; VM Dashboard
        </a>
      </div>

      <div class="sidebar-wrapper">
        <ul class="nav">

          <?php echo (basename($_SERVER['PHP_SELF']) == "host-info.php") ? '<li class="nav-item active">' : '<li class="nav-item">'; ?>
            <a class="nav-link" href="../host/host-info.php">
              <i class="material-icons">home</i>
              <p>Host</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "domain-list.php") ? '<li class="nav-item active">' : '<li class="nav-item">'; ?>
            <a class="nav-link" href="../domain/domain-list.php">
              <i class="material-icons">list</i>
              <p>Virutal Machines</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "storage-pools.php") ? '<li class="nav-item active">' : '<li class="nav-item">'; ?>
            <a class="nav-link" href="../storage/storage-pools.php">
              <i class="material-icons">storage</i>
              <p>Storage</p>
            </a>
          </li>

          <?php echo (basename($_SERVER['PHP_SELF']) == "network-list.php") ? '<li class="nav-item active">' : '<li class="nav-item">'; ?>
            <a class="nav-link" href="../network/network-list.php">
              <i class="material-icons">device_hub</i>
              <p>Networks</p>
            </a>
          </li>

        </ul>
      </div>
    </div>

    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " id="navigation-example">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href=""></a>
          </div>

          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>

          <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">

              <?php
              //Notification if software update is available
              if ($_SESSION['update_available'] == true) { ?>
              <li class="nav-item dropdown">
                <a class="nav-link" href="javscript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <span class="notification">1</span>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="../config/update.php">New Update Available</a>
                </div>
              </li>
              <?php } ?>

              <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">settings</i>
                  <p class="d-lg-none d-md-block">
                    Settings
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="../config/update.php">Update</a>
                  <a class="dropdown-item" href="../config/settings.php">Settings</a>
                </div>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <?php echo htmlentities($_SESSION['username']); ?>
                  <p class="d-lg-none d-md-block">

                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="../config/preferences.php">Preferences</a>
                  <a class="dropdown-item" href="../config/setup-password-change.php">Change Password</a>
                  <a class="dropdown-item" href="../../index.php?action=logout">Logout</a>
                </div>
              </li>

            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
