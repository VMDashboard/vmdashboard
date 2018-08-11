<?php header('Location: index.php'); ?>
<hr>
<h4>Changelog</h4>
[18.08.11] - 11.AUG.2018
*Changes to the HTML/CSS theme have improved scrollbar apperance and better use of web page realestate
*The noVNC connection is loaded from an authenticated web page.
*The tokens for the noVNC connection are now 100 character random strings, which change everytime a VM page is loaded (domain-single.php)
*The console preview on the domain-single.php is now a live noVNC connection to the machine rather than a static image

[18.07.24] - 24.JUL.2018
*removed unnessary code from pages/footer.php Started adding support for mulitple languages.

[18.07.13] - 13.JUL.2018
*Changed the location of the noVNC default certificate to /etc/ssl/self.pem

[18.07.011] - 11.JUL.2018
* Official Stable release of the OpenVM dashboard
