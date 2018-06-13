<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: ../login.php');
}


require('../header.php');
require('../navbar.php');
?>

<div class="content">
  <div class="card">
    <form action="" method="POST">
      <div class="card-header">
        <h4 class="card-title"> Test Page</h4>
      </div>
      <div class="card-body">
hello
      <?php
      $info = $lv->host_get_node_info();
      
      echo "Info:" . $info;
      var_dump($info);
      var_dump($cpu);
      var_dump($multi_cpu);
      var_dump($mem);
      ?>

      </div> <!-- end card body -->
      <div class="card-footer text-right">
        <input type="submit" class="btn btn-danger" name="action" value="Change" >
      </div>
    </form>
  </div> <!-- end card -->
</div> <!-- end content -->

<?php
require('../footer.php');
?>
