# openvm

This software is tested to run on a Ubuntu 16.04 server. Below are a list of the steps needed to setup the requirements for this software.

    Install the Ubuntu operating system
    Install the hypervisor software using the command: sudo apt install qemu-kvm libvirt-bin virtinst
    Install the LAMP package using the command: sudo apt install lamp-server^
    Install the php-xml package for parsing xml documents: sudo apt install php7.0-xml
    Install the php-imagick package for creating screenshots: sudo apt install php-imagick
    Install the libvirt-php software using the command: sudo apt install php-libvirt-php
    Edit and uncomment /etc/libvirt/qemu.conf to allow listening on 0.0.0.0. This will allow the noVNC package to work
    After making changes to the /etc/libvirt/qemu.conf file restart the service: sudo service libvirt-bin restart
    To give the www-data user permissions to use libvirt change the "unix_sock_rw_perms" option to read "0777" in the /etc/libvirt/libvirtd.conf



Troubleshooting:

VNC.
If the VNC connection is not working, make sure that noVNC/utils/launch.sh is executable as well as noVNC/utils/websockify/run.




There are still things to code:

    Delete storage drives from guest. Code is there but error says cannot attach disk. Seems like it is trying to add rather than remove
    Create new storage pool areas, including nfs, iscsi etc
    Create networking options
    Choose to autostart guest, also set order and time of autostart on a page
    Migrate domain - including: xml, storage, snapshots, etc
    Give option to resize volumes
    Give option to convert volumes to different types. Ex raw to qcow2
    Give option to upload ISO files to storage pool
    Create option to change boot order on domain
    Create option to create a disk image when creating new vm
    Setup session variables for login
    Setup database connection
    Keep track of vm and hosts stats
    Create initial setup wizard for software, username, password, database connection, etc
    Setup network filters
