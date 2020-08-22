---
tags: [BMP280, FT232H, Python3]
author: Roland Smith
---

# Reading temperature and pressure with a BMP280 and an FT232H

> <https://github.com/rsmith-nl/ft232-bmp280>

## Introduction

This code was written to get the Adafruit BMP280 breakout board to work with my computer, using an Adafruit FT232H breakout board as a USB ↔ SPI or USB ↔ I²C bridge.

The `bmp280.py` module supports both SPI and I²C connections between the FT232H and the BMP280.
If uses pyftdi_ to handle those connections.
This module in turn requires pyserial_ and pyusb_.
The advantage of `pyftdi` is that it is a pure python solution.
It does not require native libraries which makes installing it easier.

* _pyftdi: <https://github.com/eblot/pyftdi>
* _pyusb: <https://github.com/pyusb/pyusb>
* _pyserial: <https://github.com/pyserial/pyserial>

Additionally, there is a program called `bmp280-monitor-spi.py` that can query the data from the sensor at a configurable interval and write it to a file.

The datasheet for the sensor, the Bosch code on github and the Adafruit CircuitPython were used as references in writing this code.

This software has been written for Python 3 on the FreeBSD operating system.
I expect it will work on other POSIX systems, and maybe even on ms-windows.
But I haven't tested that.

## Wiring the BMP280

Both the BMP280 and the FT232H breakout boards were placed on a small
breadboard. I connected the breakout boards via SPI to run
`bmp280-monitor-spi.py`:

* 5V to VIN
* GND to GND
* D0 to SCK
* D1 to SDI
* D2 to SDO
* D3 to CS

Note that for this to work, any *native* driver for FTDI chips needs to be unloaded and disabled.
On FreeBSD the first is achieved by running `kldunload uftdi.ko` as root.
The second step is accomplished by commenting out the `nomatch` statement in `/etc/devd/usb.conf` that loads `uftdi` driver and restarting `devd` by running `service devd restart` as root.

## The module

Assuming you are in the directory where the `bmp280.py` module lives, and you have installed `pyftdi`, you can use it as follows.
First copy the following into an IPython 3 session or a Python 3 interpreter.

```python
    from time import sleep
    from pyftdi.spi import SpiController
    from bmp280 import Bmp280spi
    ctrl = SpiController()
    ctrl.configure('ftdi://ftdi:232h/1')  # Assuming there is only one FT232H.
    spi = ctrl.get_port(0)  # Assuming D3 is used for chip select.
    spi.set_frequency(1000000)
    bmp280 = Bmp280spi(spi)
```

Now you are ready to measure.

```python
    while True:
        print(bmp280.read())
        sleep(5)
```

This should print a (temperature, pressure) tuple every
five seconds.


## The monitoring program

The `bmp280-monitor-spi.py` program is designed to be started from the command line, where it should probably be started so as to run in the background.
Run `./bmp280-monitor-spi.py -h` to see the optional and required parameters.

An example:

```console
    ./bmp280-monitor-spi.py -i 900 /tmp/bmp280-{}.d
```

This will write data to a file in `/tmp` every fifteen minutes.
The data will look like this:

```
    # BMP280 data.
    # Started monitoring at 2018-04-24T23:46:19Z.
    # Per line, the data items are:
    # * UTC date and time in ISO8601 format
    # * Temperature in °C
    # * Pressure in Pa
    2018-04-24T23:46:19Z 24.6 100809
    2018-04-24T23:51:19Z 24.6 100811
    2018-04-24T23:56:19Z 24.7 100787
    2018-04-25T00:01:19Z 24.6 100793
    2018-04-25T00:06:19Z 24.6 100806
    2018-04-25T00:11:19Z 24.5 100793
```

