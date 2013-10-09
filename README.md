mentor-app
==========

Mentor matching application

Vagrant
=======

There are two bundled vagrant set ups. The first is for php5.4 and the other for php5.5. In order to use them you will
need to install Vagrant (http://vagrantup.com). It is also recommended you use VirtualBox (http://www.virtualbox.org) as
the Vagrant provider.

To get the Vagrant system running, go to the relevant directory (vagrant-mentor-app-php5.x) and run the following:

$ vagrant up

This will start the vagrant instance for you. You can have both running at the same time without any issues. To access
the web server on either you will need to open your browser and go to the relevant URL.

PHP 5.4 : http://localhost:8080
PHP 5.5 : http://localhost:8081

If you need to access your Vagrant machine at any time you can go in to the relevant directory and run:

$ vagrant ssh

When you are finished for the day, go to the relevant directory and run:

$ vagrant halt

When you are finished forever, you can completely destroy the machine by running:

$ vagrant destroy
