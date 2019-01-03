<?php 
function getOSInformation() { //https://stackoverflow.com/questions/1482260/how-to-get-the-os-on-which-php-is-running
    if (false == function_exists("shell_exec") || false == is_readable("/etc/os-release")) {
        return null;
    }

    $os         = shell_exec('cat /etc/os-release');
    $listIds    = preg_match_all('/.*=/', $os, $matchListIds);
    $listIds    = $matchListIds[0];

    $listVal    = preg_match_all('/=.*/', $os, $matchListVal);
    $listVal    = $matchListVal[0];

    array_walk($listIds, function(&$v, $k){
        $v = strtolower(str_replace('=', '', $v));
    });

    array_walk($listVal, function(&$v, $k){
        $v = preg_replace('/=|"/', '', $v);
    });

    return array_combine($listIds, $listVal);
}

$os_info = getOSInformation();
$host_os = $os_info['name'];

?>


<body class=" <?php if($_SESSION['themeColor'] == "dark-edition") { echo "main-dark"; } ?> ">
    <nav class="navbar navbar-dark bg-dark sticky-top flex-md-nowrap p-0 <?php if($_SESSION['themeColor'] == "dark-edition") { echo "main-dark"; } ?> ">
      <a class="navbar-brand navbar-dark col-sm-3 col-md-2 mr-0" href="../../index.php"><img src="../../assets/img/squarelogo.png" width="28px"> &ensp; VM Dashboard</a>
      <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
      <ul class="navbar-nav px-3">
        <?php if ($_SESSION['update_available'] == true) { ?>
            <li class="nav-item">
                <a class="nav-link" href="../config/update.php">New Update Available</a>
            </li>
        <?php } ?>

        <li class="nav-item">
            <?php
                if ($_SESSION['update_available'] == true) {
                    echo "<a class=\"nav-link\" style=\"color:orange;\" href=\"../config/update.php\">Update</a>";
                } else {
                    echo "<a class=\"nav-link\" href=\"../config/update.php\">Update</a>";
                }
            ?>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../config/settings.php">Settings</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../config/preferences.php">Preferences</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="../../index.php?action=logout">Sign out</a>
        </li>

      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-dark navbar-dark sidebar">
            
          <div class="sidebar-sticky">
            <ul class="nav flex-column mb-2">

                <li>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle text-muted host-heading">
                            <span style="font-size: 18px; color: #999; padding-right:2px;">
                                <?php
                                 if ($host_os == "Ubuntu")
                                 echo "<i class=\"fab fa-ubuntu\"></i>";
                                 elseif (preg_match('Red Hat', $host_os))
                                 echo "<i class=\"fab fa-redhat\"></i>";
                                 elseif ($host_os == "CentOS Linux")
                                 echo "<i class=\"fab fa-centos\"></i>";
                                 elseif ($host_os == "Fedora")
                                 echo "<i class=\"fab fa-fedora\"></i>";
                                 elseif (preg_match('SUSE', $host_os))
                                 echo "<i class=\"fab fa-suse\"></i>";
                                 else 
                                 echo "<i class=\"fab fa-linux\"></i>";
                                ?>
                            </span>      
                            localhost
                        </a>
                    </h6>
                    <ul class="collapse show list-unstyled" id="pageSubmenu">
                        <li class="nav-item">
                            <a class="nav-link" href="../host/host-info.php">
                                <span data-feather="server"></span>
                                Host Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../domain/domain-list.php">
                                <span data-feather="layers"></span>
                                Virtual Machines
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../storage/storage-pools.php">
                                <span data-feather="database"></span>
                                Storage
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../network/network-list.php">
                                <span data-feather="link-2"></span>
                                Networking
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
          </div>
        </nav>