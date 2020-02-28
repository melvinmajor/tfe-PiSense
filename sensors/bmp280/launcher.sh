#!/bin/sh
#This script is used to automaticaly execute the script bmp280.py.
#The bmp280 will connect the BMP sensor and the RaspBerry Pi.

cd /PiSense/sensors/bmp280
sudo python3 bmp280sensor.py
