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
file_put_contents( 'progress.txt', '' );
$targetFile = fopen( 'testfile.iso', 'w' );
$ch = curl_init( 'http://releases.ubuntu.com/18.04/ubuntu-18.04-live-server-amd64.iso' );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
curl_setopt( $ch, CURLOPT_FILE, $targetFile );
curl_exec( $ch );
fclose( $ch );
function progressCallback( $download_size, $downloaded_size, $upload_size, $uploaded_size )
{
    static $previousProgress = 0;

    if ( $download_size == 0 )
        $progress = 0;
    else
        $progress = round( $downloaded_size * 100 / $download_size );

    if ( $progress > $previousProgress)
    {
        $previousProgress = $progress;
        $fp = fopen( 'progress.txt', 'a' );
        fputs( $fp, "$progress\n" );
        fclose( $fp );
    }
}
?>

      </div>
    </div>
  </div>
</div>

<?php
require('../footer.php');
?>
