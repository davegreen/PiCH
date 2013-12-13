#!/bin/bash
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root, use 'sudo ./setup.sh'" 
   exit 1
fi

echo "Make sure we have the latest version of system."
apt-get update && apt-get upgrade -y && apt-get dist-upgrade -y && apt-get clean
echo "System update complete."

echo "Installing prerequisites".
apt-get install -y git python-pip python-dev python-imaging python-mysqldb mysql-server lighttpd php5-cgi phpmyadmin
cd ~/
git clone git://github.com/doceme/py-spidev
cd py-spidev/
python setup.py install
pip install wiringpi
cd ..
rm -Rf py-spidev/

git clone git://git.drogon.net/wiringPi
cd wiringPi
./build
cd ..
rm -Rf wiringPi/

echo "Restart lighttpd for php fastcgi".
/etc/init.d/lighttpd force-reload

echo "Installing Raspberry Pi firmware updater."
wget http://goo.gl/1BOfJ -O /usr/bin/rpi-update
chmod +x /usr/bin/rpi-update

echo "Updating RaspberryPi firmware using the rpi-update tool."
rpi-update

#PiCH bootscript needs to be run as sudo for inital setup each boot.

echo "Setup done! If there were no errors, reboot the pi to complete setup."