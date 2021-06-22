#!/bin/sh
#This script is used to automatically execute related Python code around BME280 and SDS011 sensors.

# Open Konsole Terminal with 2 tabs
# First tab is BME280 sensor, second tab is SDS011 sensor
konsole --new-tab --hold tabtitle="PiSense BME280" -e "bash -c \"cd ~/Git/tfe-PiSense/sensors; sudo python3 bme280/bme280sensor.py\"" &
konsole --new-tab --hold tabtitle="PiSense SDS011" -e "bash -c \"cd ~/Git/tfe-PiSense/sensors; sudo python sds011/sds011sensor.py\""

