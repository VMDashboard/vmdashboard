<?php
// If the SESSION has not started, start it now
if (!isset($_SESSION)) {
    session_start();
}
// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  header('Location: login.php');
}
// We are now going to grab any POST data and put in in SESSION data, then clear it.
// This will prevent and reloading the webpage to resubmit and action.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['password'] = $_POST['password'];
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
// Time to bring in the header, sidebar, and navigation menus
require('header.php');
require('navigation.php');
?>

<script>
function checkPassword()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match.
        //Set the color to the good color and inform
        //the user that they have entered the correct password
        pass2.style.backgroundColor = "#ffffff";
        message.style.color = goodColor;
        message.innerHTML = "<p>Passwords Match!</p>"
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "<p>Passwords Do Not Match!</p>"
    }
}
</script>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Host</h3>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Host: <?php echo $hn; ?></h2>

          <div class="clearfix"></div>
        </div>

        <div class="x_content">

          <div class="col-md-9 col-sm-9 col-xs-12">
            <?php


            if (array_key_exists('password', $_SESSION)) {
              $name = $_SESSION['name'];
              if ($_SESSION['action'] == 'dumpxml')
                $ret = 'XML dump of node device <i>'.$name.'</i>:<br/><br/>'.htmlentities($lv->get_node_device_xml($name, false));
                //Unset the SESSION variables
                unset($_SESSION['password']);

            }

            //If we have returned XML data, display it
            if ($ret){
              echo "<pre>$ret</pre>";
              echo "<br /><br />";
            }

            // Adding the user
            $sql = "INSERT INTO openvm_users (username, email, password)
              VALUES ('$username', '$email', '$hash');";
              // Executing the SQL statement
              if ($conn->query($sql) === TRUE) {
                header('Location: ../index.php');
              } else {
                echo "Error: " . $sql . " " . $conn->error;
              }

              $conn->close();



              <form method="post" action="setup-user.php">
                <h1>Create Account</h1>
                <div>
                  <input type="text" name="username" class="form-control" placeholder="Username" required="" />
                </div>
                <div>
                  <input type="email" name="email" class="form-control" placeholder="Email" required="" />
                </div>
                <div>
                  <input type="password" name="password" class="form-control" placeholder="Password" required="" id="pass1"/>
                </div>
                <div>
                  <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required="" id="pass2" onkeyup="checkPassword();"/>
                </div>
                <span id="confirmMessage" class="confirmMessage"></span>
                <div>
                  <input style="float:none;margin:0px;" type="submit" name="account" value="Finish" class="btn btn-default submit">
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                  <p class="change_link">New to site?
                    <a href="#" class="to_register"> View tutorials </a>
                  </p>

                  <div class="clearfix"></div>
                  <br />

                  <div>
                    <span style="font-size:35px;">open<font style="color:#FF8C00;">VM</font></span>
                    <p>©2018 All Rights Reserved.</p>
                  </div>
                </div>
              </form>


            ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        <!-- /page content -->

<?php require('footer.php');?>
