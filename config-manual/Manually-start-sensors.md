---
subtitle: Manually start sensors on the device
date: 20 june 2021
keywords: [PiSense, IoT, sensors, dev, tutorial, configuration, console, python]
footer-left: Manual - Manually start sensors on Raspberry Pi devices
toc-title: Manual - how to manually start sensors onto Raspberry Pi devices
---

# Manually launch sensors onto Raspberry Pi devices

Once the Raspberry Pi has started and you're connected via *VNC Viewer* or *TeamViewer*, launch **Konsole** to access Linux/Raspbian terminal.

* Go to the right folder: `cd Git/tfe-PiSense/sensors/`,
* Launch BME680/BME280/BMP280 code according to the sensor connected on PiSense: `sudo python3 bme680/bme680sensor.py`
* Open a new tab in Konsole: <kbd>New Tab (Ctrl+Shift+T)</kbd> > <kbd>Fish (F)</kbd>,
* Launch SDS011 code if the sensor is connected on PiSense: `sudo python sds011/sds011sensor.py`.

That's it!

## Automatically launch sensors onto Raspberry Pi devices

If you want to automatically launch both BME680 and SDS011 sensor, a small script is available and runnable inside root folder.

BME680 + SDS011 launcher: `pi_bme680-sds011launcher.sh`

Other launchers (BME280 and BMP280 coupled with SDS011) will shortly be made available.
