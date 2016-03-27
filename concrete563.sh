#!/usr/bin/env bash

## Update packages
apt-get update
apt-get -y install python-software-properties
apt-add-repository ppa:ondrej/php5-oldstable
apt-get update

# MySQL
export DEBIAN_FRONTEND=noninteractive
apt-get -q -y install mysql-server mysql-client
mysqladmin -u root password vagrant
/etc/init.d/mysql restart

# Apache
apt-get -y install apache2

# PHP5
apt-get -y install php5 libapache2-mod-php5

## PHP Modules
apt-get -y install php5-mysql php5-curl php5-gd php5-intl php-pear php5-imagick php5-imap php5-mcrypt php5-memcache php5-ming php5-ps php5-pspell php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl

# Run Apache as Vagrant user/group
cat << EOF >> /etc/apache2/httpd.conf
User vagrant
Group vagrant
EOF

#PHPMyAdmin
apt-get -y install phpmyadmin
echo "Include /etc/phpmyadmin/apache.conf" >> /etc/apache2/apache2.conf

# Restart Apache
/etc/init.d/apache2 restart

# Symlinks
rm -rf /var/www
ln -fs /vagrant /var/www

# CURL 
apt-get install -y curl

# UNZIP
apt-get -y install unzip
 
# Concrete5 563
curl -LO https://github.com/concrete5/concrete5/archive/master.zip
unzip master.zip
mv concrete5-master/web/* /var/www

# Create database
mysql -u root -pvagrant -e "create database vagrant";

# Install concrete5
php concrete5-master/cli/install-concrete5.php --db-server=localhost --db-username=root --db-password=vagrant --db-database=vagrant --admin-password=vagrant --admin-email=oliver@eantics.co.uk --starting-point=standard --target=/var/www

# Restart Apache
/etc/init.d/apache2 restart

# composer
php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === '41e71d86b40f28e771d4bb662b997f79625196afcca95a5abf44391188c695c6c1456e16154c75a211d238cc3bc5cb47') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
