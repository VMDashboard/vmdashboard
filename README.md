openVM is currently tested on the Ubuntu 18.04 operating systems. Once openVM has reached a stable version it will be tested on other Linux systems. Below are the instructions for downloading and setting up openVM.

On Ubuntu 18.04 server, install the QEMU + KVM hypervisor  using the following command:
<code>sudo apt install qemu-kvm libvirt-bin</code>

Install the LAMP package to use the Apache web server, MySQL database, and PHP programming language. Use the following command:
<code>sudo apt install lamp-server^</code>

There are a few additional PHP packages needed to run openVM. PHP will need to control the VMs using libvirt. Use the following command to install the packages:
<code>sudo apt install php7.0-xml php-libvirt-php</code>

To use VNC to connect into your virtual machines, you will need to edit the <strong>/etc/libvirt/qemu.conf</strong> file. Be sure to allow listening on IP address <strong>0.0.0.0</strong> by uncommenting the line #vnc_listen = "0.0.0.0".

The web server user account on Ubuntu is called www-data. This account will need to have permissions to work with libvirt. To do this, open the <strong>/etc/libvirt/libvirtd.conf</strong> file and change the <strong>unix_sock_rw_perms</strong> option to read <strong>0777</strong>.

Restart the libvirt server using the command:
<code>sudo service libvirt-bin restart</code>

Download the openVM software to the root directory of your web server. The default location is /var/www/html/ in Ubuntu.
<code>cd /var/www/html</code>
<code>sudo git clone https://github.com/PenningDevelopment/openVM.git</code>

Use a web browser to navigate to your server's IP address or domain name.

<hr />

Known issues:
<ul>
 	<li>Disk volumes will not delete from domain through interface. Can still be accomplished by editing the domain XML.</li>
 	<li>Network devices will not delete from domain through interface. Can still be accomplished by editing the domain XML.</li>
 	<li>Network devices do not display correctly in domain-single.php.</li>
</ul>
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
 	<li>Include domain stats such as memory, disk , cpu, etc</li>
</ul>
