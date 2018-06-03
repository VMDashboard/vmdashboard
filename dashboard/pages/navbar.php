  <body>
    <div class="wrapper">
      <div class="sidebar">
      <!--
          Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
      -->

        <div class="logo">
          <a href="http://www.creative-tim.com" class="simple-text logo-mini">
              CT
          </a>

          <a href="http://www.creative-tim.com" class="simple-text logo-normal">
              Creative Tim
          </a>


        <div class="sidebar-wrapper">
          <div class="user">
              <div class="photo">
                  <img src="../assets/img/james.jpg" />
              </div>
              <div class="info">
                  <a data-toggle="collapse" href="#collapseExample" class="collapsed">
                      <span>
                          Chet Faker
                          <b class="caret"></b>
                      </span>
                  </a>
                  <div class="clearfix"></div>
                  <div class="collapse" id="collapseExample">
                      <ul class="nav">
                          <li>
                              <a href="#">
                                  <span class="sidebar-mini-icon">MP</span>
                                  <span class="sidebar-normal">My Profile</span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  <span class="sidebar-mini-icon">EP</span>
                                  <span class="sidebar-normal">Edit Profile</span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  <span class="sidebar-mini-icon">S</span>
                                  <span class="sidebar-normal">Settings</span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>
          <ul class="nav">

              <li class="active">
                  <a href="../examples/dashboard.html">
                      <i class="nc-icon nc-layout-11"></i>
                      <p>Example 1</p>
                  </a>
              </li>

              <li>
                  <a data-toggle="collapse" href="#pagesExamples">
                      <i class="nc-icon nc-laptop"></i>
                      <p>Example 2
                         <b class="caret"></b>
                      </p>
                  </a>

                  <div class="collapse" id="pagesExamples">
                      <ul class="nav">
                          <li>
                              <a href="../examples/pages/pricing.html">
                                  <span class="sidebar-mini-icon">C1</span>
                                  <span class="sidebar-normal">Collapse 1</span>
                              </a>
                          </li>
                          <li>
                              <a href="../examples/pages/timeline.html">
                                  <span class="sidebar-mini-icon">C2</span>
                                  <span class="sidebar-normal">Collapse 2</span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>
          </ul>
        </div>
      </div>

      <div class="main-panel">
        <nav class="navbar navbar-expand-lg navbar-absolute bg-white fixed-top">
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
              <a class="navbar-brand" href="#pablo">Dashboard</a>
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
                <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="nc-icon nc-zoom-split"></i>
                  </div>
                </div>
              </div>
            </form>

            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link btn-magnify" href="#pablo">
                  <i class="nc-icon nc-layout-11"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Stats</span>
                  </p>
                </a>
              </li>
              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="nc-icon nc-bell-55"></i>
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
                <a class="nav-link btn-rotate" href="#pablo">
                  <i class="nc-icon nc-settings-gear-65"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Account</span>
                  </p>
                </a>
              </li>
            </ul>

            </div>
          </div>
        </nav>
