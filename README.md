# openvm

openVM is currently tested on the Ubuntu 16.04 and 18.04 operating systems. Once openVM has reached a stable version it will be tested on other Linux systems. Below are the instructions for downloading and setting up openVM.

On Ubuntu 18.04 server, install the QEMU + KVM hypervisor  using the following command:
sudo apt install qemu-kvm libvirt-bin

Install the LAMP package to use the Apache web server, MySQL database, and PHP programming language. Use the following command:
sudo apt install lamp-server^

There are a few additional PHP packages needed to run openVM. PHP will need to control the VMs using libvirt. Use the following command to install the packages:
sudo apt install php7.0-xml php-libvirt-php

To use VNC to connect into your virtual machines, you will need to edit the /etc/libvirt/qemu.conf file. Be sure to allow listening on IP address 0.0.0.0 by uncommenting the line #vnc_listen = "0.0.0.0".

The web server user account on Ubuntu is called www-data. This account will need to have permissions to work with libvirt. To do this, open the /etc/libvirt/libvirtd.conf file and change the unix_sock_rw_perms option to read 0777.

Restart the libvirt server using the command:
sudo service libvirt-bin restart

Download the openVM software to the root directory of your web server. The default location is /var/www/html/ in Ubuntu.
cd /var/www/html
sudo git clone https://github.com/PenningDevelopment/openVM.git

Use a web browser to navigate to your server's IP address or domain name.


Known issues:
  Disk volumes will not delete from domain through interface. Can still be accomplished by editing the domain XML.
  Network devices will not delete from domain through interface. Can still be accomplished by editing the domain XML.
  Network devices do not display correctly in domain-single.php.

Features for future releases:
  Include creating storage pools that use NFS, SMB, and iSCSI.
  Allow for domain migration to another server
  Allow for multiple connections to other hosts, including ESX
  Allow for resizing of disk volumes
  Allow for autostarting of guests
  Coverting volumes from one type to another. For example, raw to qcow2
  Changing boot order from interface. Can now be accomplished by editing the domain XML.
  Use session variables and database to control login and registration pages
  Include domain stats such as memory, disk , cpu, etc
