#!/bin/sh
# This one will automatically clone the repo with all the code for the Raspberry
# It will also install the necessary modules linked to BME680 sensor

sudo apt update -y
sudo apt upgrade -y
sudo apt install python python3 python-pip python3-pip -y

cd ~/
git clone https://github.com/pimoroni/bme680
cd bme680/library
sudo python setup.py install
sudo pip install bme680

cd /
git clone https://github.com/melvinmajor/https://github.com/melvinmajor/tfe-PiSense.git PiSense
