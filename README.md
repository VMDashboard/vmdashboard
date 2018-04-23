openVM is currently tested on the Ubuntu 16.04 and 18.04 operating systems. Once openVM has reached a stable version it will be tested on other Linux systems. Below are the instructions for downloading and setting up openVM.

On the Ubuntu server, install the QEMU + KVM hypervisor  using the following command:
<code>sudo apt install qemu-kvm libvirt-bin</code>

Install the LAMP package to use the Apache web server, MySQL database, and PHP programming language. Use the following command:
<code>sudo apt install lamp-server^</code>

There are a few additional PHP packages needed to run openVM. PHP will need to control the VMs using libvirt. Use the following command to install the packages:
<code>sudo apt install php-xml php-libvirt-php</code>

If you are using Ubuntu 16.04 you will also need to install the php imagick package. It will be already installed with PHP on Ubuntu 18.04:
<code>sudo apt install php-imagick</code>

The built-in vnc connection requires python. To install it use the following command:
<code>sudo apt install python</code>

To use VNC to connect into your virtual machines, you will need to edit the <strong>/etc/libvirt/qemu.conf</strong> file. Be sure to allow listening on IP address <strong>0.0.0.0</strong> by uncommenting the line #vnc_listen = "0.0.0.0".
<code> sudo nano /etc/libvirt/qemu.conf</code>

The web server user account on Ubuntu is called www-data. This account will need to have permissions to work with libvirt. To do this, add the www-data user to the libvirtd group.
<code>sudo adduser www-data libvirtd</code>

Restart the server
<code>sudo reboot</code>

The git software should be installed on your server. If it is not, you can use the following command to download git.
<code>sudo apt install git</code>

Download the openVM software to the root directory of your web server. The default location is /var/www/html/ in Ubuntu.
<code>cd /var/www/html</code><br>
<code>sudo git clone https://github.com/PenningDevelopment/openVM.git</code>

Change the ownership of the openVM directory to the web server user (www-data).
<code> sudo chown -R www-data:www-data /var/www/html/openVM </code>

Use a web browser to navigate to your server's IP address or domain name. Add /openVM to the end of the URL. For example: https://192.168.1.2/openVM

<hr />


Features for future releases:
<ul>
 	<li>Include creating storage pools that use NFS, SMB, and iSCSI.</li>
 	<li>Allow for domain migration to another server</li>
 	<li>Allow for multiple connections to other hosts, including ESX</li>
 	<li>Allow for resizing of disk volumes</li>
 	<li>Allow for autostarting of guests</li>
 	<li>Converting volumes from one type to another. For example, raw to qcow2</li>
 	<li>Changing boot order from interface. Can now be accomplished by editing the domain XML.</li>
 	<li>Use session variables and database to control login and registration pages</li>
 	<li>Include domain stats such as memory, disk, cpu, etc</li>
</ul>
