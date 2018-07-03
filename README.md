*** June 18 update - the tokens file for VNC connections has been changed to prevent outside users from discovering uuids and vnc ports of VMs. If you update from a prior version you will need to restart the server or the python service that is running noVNC. If you have a earlier version of the software you may need to delete the tokens.list file in the root directory of your web server. Also change the ownership of the new tokens.php file to www-data:www-data



openVM is currently tested on the Ubuntu 16.04 and 18.04 operating systems. Once openVM has reached a stable version it will be tested on other Linux systems.

At this moment it is being actively developed and may be different from the stable version coming soon. As of 14-Jun-2018 it is about 95% ready for the first release.

To download and install the software, follow the guide at: https://openvm.tech/download/

Screenshots:
<h2>Host Information</h2>
![image](https://openvm.tech/wp-content/uploads/2018/07/Screen-Shot-2018-07-02-at-6.17.22-PM.png)

<h2>New Virtual Machine</h2>
![image](https://openvm.tech/wp-content/uploads/2018/07/Screen-Shot-2018-07-02-at-6.22.30-PM.png)
