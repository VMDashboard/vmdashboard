openVM is currently tested on the Ubuntu 16.04 and 18.04 operating systems. Once openVM has reached a stable version it will be tested on other Linux systems. Below are the instructions for downloading and setting up openVM.

Installing the necessary packages

On the Ubuntu server, install the QEMU + KVM hypervisor  using the following command:
sudo apt install qemu-kvm libvirt-bin

Install the LAMP package to use the Apache web server, MySQL database, and PHP programming language. Use the following command:
sudo apt install lamp-server^

There are a few additional PHP packages needed to run openVM. PHP will need to control the VMs using libvirt. Use the following command to install the packages:
sudo apt install php-xml php-libvirt-php

If you are using Ubuntu 16.04 you will also need to install the php imagick package. It will be already installed with PHP on Ubuntu 18.04:
sudo apt install php-imagick

The built-in VNC connection requires python. To install it use the following command:
sudo apt install python

The git software should be installed on your server. If it is not, you can use the following command to download git.
sudo apt install git
Configuring files and permissions

To use VNC to connect into your virtual machines, you will need to edit the /etc/libvirt/qemu.conf file. Be sure to allow listening on IP address 0.0.0.0 by uncommenting the line #vnc_listen = "0.0.0.0" and saving the file.
sudo nano /etc/libvirt/qemu.conf

The web server user account on Ubuntu is called www-data. This account will need to have permissions to work with libvirt. To do this, add the www-data user to the libvirtd group.
sudo adduser www-data libvirtd

Download the openVM software to the root directory of your web server. The default location is /var/www/html/ in Ubuntu.
cd /var/www/html
sudo git clone https://github.com/PenningDevelopment/openVM.git

Change the ownership of the openVM directory to the web server user (www-data).
sudo chown -R www-data:www-data /var/www/html/openVM

Creating a database

We will need a MySQL database for openVM to work with. To log into MySQL use the following command:
mysql -u root -p

You will be prompted for your the password that was setup for the root user on MySQL. Once logged in, create a new database. I will name it openvm.
CREATE DATABASE openvm;

Now create a user for openVM to use. You could use the root user and password, but that is never advised. I will create a new user named openvm with a password of supersecretpassword.
CREATE USER 'openvm'@'localhost' IDENTIFIED BY 'supersecretpassword';

Change the permissions of the new user to have full access to the database tables.
GRANT ALL PRIVILEGES ON openvm.* to 'openvm'@'localhost';

The new privileges should be applied, but sometimes you will need to flush the privileges so that they can be reloaded into the MySQL database. To do this use the following command:
FLUSH PRIVILEGES;

To exit MySQL, type quit or use the EXIT; statement.
EXIT;

Connecting to openVM

You will need to restart your server before you can use the openVM software.
sudo reboot

Once rebooted, use a web browser to navigate to your server's IP address or domain name. Add /openVM to the end of the URL. For example: https://192.168.1.2/openVM

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
