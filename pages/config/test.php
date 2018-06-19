<?php
// If the SESSION has not been started, start it now
if (!isset($_SESSION)) {
    session_start();
}

// If there is no username, then we need to send them to the login
if (!isset($_SESSION['username'])){
  $_SESSION['return_location'] = $_SERVER['PHP_SELF']; //sets the return location used on login page
  header('Location: ../login.php');
}


require('../header.php');
require('../navbar.php');


?>

<div class="content">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"> Test</h4>
    </div>
    <div class="card-body">
      <div class="row">


        <?php
        //output buffer
        ob_start();
        //create javascript progress bar
        echo '<script>
        function updateProgress(percentage) {
            document.getElementById(\'progress\').value = percentage;
        }
        </script>
            <progress id="prog" value="0" max="100.0"></progress>

            <div class="progress">
              <div id="prog2" class="progress-bar progress-bar-danger" id="prog" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

        ';



        //initilize progress bar
        ob_flush();
        flush();
        //save progress to variable instead of a file
        $temp_progress = '';
        $targetFile = fopen( 'centos.iso', 'w' );
        $ch = curl_init( 'http://centos.mbni.med.umich.edu/mirror/7/isos/x86_64/CentOS-7-x86_64-Minimal-1804.iso' );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_exec( $ch );
        fclose( $targetFile );
        //must add $resource to the function after a newer php version. Previous comments states php 5.5
        function progressCallback( $resource, $download_size, $downloaded_size, $upload_size, $uploaded_size )
        {
            static $previousProgress = 0;

            if ( $download_size == 0 ) {
                $progress = 0;
            } else {
                $progress = round( $downloaded_size * 100 / $download_size );
        	}

            if ( $progress > $previousProgress)
            {
                $previousProgress = $progress;
                $temp_progress = $progress;
            }
            //update javacsript progress bar to show download progress
        	echo '<script>document.getElementById(\'prog\').value = '.$progress.';</script>';
          echo '<script>document.getElementById(\'prog2\').aria-valuenow = '.$progress.';</script>';

        	ob_flush();
            flush();
            //sleep(1); // just to see effect
        }
        //if we get here, the download has completed
        echo "Done";
        //flush just to be sure
        ob_flush();
        flush();
        ?>


      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
