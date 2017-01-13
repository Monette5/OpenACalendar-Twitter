#!/usr/bin/env bash

sudo apt-get update
sudo apt-get install -y php5-cli php5-curl git curl zip  phpunit

mkdir /home/vagrant/bin
cd /home/vagrant/bin
wget -q https://getcomposer.org/composer.phar

cd /vagrant
php /home/vagrant/bin/composer.phar install
