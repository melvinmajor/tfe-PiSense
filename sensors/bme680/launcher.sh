#!/bin/sh
#This script is used to automatically execute the script bme680.py.
#The bme680 will connect the BME sensor and the RaspBerry Pi.

cd /PiSense/sensors/bme680
sudo python3 bme680sensor.py
