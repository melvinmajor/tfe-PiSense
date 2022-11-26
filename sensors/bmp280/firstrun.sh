#!/bin/sh
# This one will automatically clone the repo with all the code for the Raspberry
# It will also install the necessary modules linked to BMP280 sensor

sudo apt update -y
sudo apt upgrade -y
sudo apt install python3 python3-pip -y
sudo pip install RPI.GPIO adafruit-blinka adafruit-circuitpython-bmp280
sudo pip3 install RPI.GPIO adafruit-blinka adafruit-circuitpython-bmp280
sudo pip install --upgrade setuptools
cd /
git clone https://github.com/melvinmajor/tfe-PiSense.git PiSense
