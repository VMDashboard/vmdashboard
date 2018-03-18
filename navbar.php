<body class="">
    <div class="wrapper ">
        <div class="sidebar" data-color="orange">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
            <div class="logo">
                <a href="https://github.com/PenningDevelopment/openvm" class="simple-text logo-normal">
                    openVM&trade;
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">


					<?php echo (basename($_SERVER['PHP_SELF']) == "guests.php") ? '<li class="active">' : '<li>'; ?>
                        <a href="guests.php">
                            <i class="now-ui-icons design_bullet-list-67"></i>
                            <p>VM Guests</p>
                        </a>
                    </li>


                    <?php echo (basename($_SERVER['PHP_SELF']) == "storage-pools.php") ? '<li class="active">' : '<li>'; ?>
                        <a href="storage.php">
                            <i class="fas fa-database"></i>
                            <p>Storage</p>
                        </a>
                    </li>


                    <?php echo (basename($_SERVER['PHP_SELF']) == "networking.php") ? '<li class="active">' : '<li>'; ?>
                        <a href="networking.php">
                            <i class="now-ui-icons design_vector"></i>
                            <p>Networking</p>
                        </a>
                    </li>


                    <?php echo (basename($_SERVER['PHP_SELF']) == "host.php") ? '<li class="active">' : '<li>'; ?>
                        <a href="host.php">
                            <i class="now-ui-icons tech_laptop"></i>
                            <p>Host Info</p>
                        </a>
                    </li>

                </ul>
            </div>
        </div>


        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent  navbar-absolute bg-primary fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-toggle">
                            <button type="button" class="navbar-toggler">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </button>
                        </div>
                        <a class="navbar-brand" href="#">openVM&trade;</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                        <span class="navbar-toggler-bar navbar-kebab"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">
                        <form>
                            <div class="input-group no-border">
                                <input type="text" value="" class="form-control" placeholder="Search...">
                                <span class="input-group-addon">
                                    <i class="now-ui-icons ui-1_zoom-bold"></i>
                                </span>
                            </div>
                        </form>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="now-ui-icons media-2_sound-wave"></i>
                                    <p>
                                        <span class="d-lg-none d-md-block">Stats</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="now-ui-icons location_world"></i>
                                    <p>
                                        <span class="d-lg-none d-md-block">Some Actions</span>
                                    </p>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pablo">
                                    <i class="now-ui-icons users_single-02"></i>
                                    <p>
                                        <span class="d-lg-none d-md-block">Account</span>
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
