mentor-app
==========

Mentor matching application

Vagrant
=======

The system is bundled with a Vagrant set up, so you can get up and running quickly and in a common environment.

To get the Vagrant system running, go to vagrant-mentor-app-php5.4 and run the following:

$ vagrant up

This will start the vagrant instance for you. To access the web server on either you will need to open your browser and go to:

http://localhost:8080/
OR
http://mentorapp.dev:8080/

To access mentorapp.dev you will need to add the following to your hosts file:

127.0.0.1   mentorapp.dev

If you need to access your Vagrant machine at any time you can go in to the relevant directory and run:

$ vagrant ssh

When you are finished for the day, go to the relevant directory and run:

$ vagrant halt

When you are finished forever, you can completely destroy the machine by running:

$ vagrant destroy

Database Migration
==================

In order to keep control of the database we use a system called Phinx.
Full documentation can be found at: http://docs.phinx.org/en/latest/

To create a change from the /var/www directory run the following command:

$ bin/phinx create <Name for migration>

To check the status (what's not been run yet, etc) run the following command:

$ bin/phinx status -e development

To apply outstanding migrations run the following:

$ bin/phinx migrate -e development

If you don't specify the "-e development" then it will default to development with a warning.
