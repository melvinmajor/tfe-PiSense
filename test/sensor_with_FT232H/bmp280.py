# file: BMP280.py
# vim:fileencoding=utf-8:fdm=marker:ft=python
#
# Copyright © 2018 R.F. Smith <rsmith@xs4all.nl>.
# SPDX-License-Identifier: MIT
# Created: 2018-04-08T22:38:40+0200
# Last modified: 2018-04-28T18:09:30+0200
"""
Code to use a BMP280 with FT232H using SPI or I²C connection.
Both connections use the pyftdi API.
"""

from enum import IntEnum
from time import sleep


class Reg(IntEnum):
    """Registers of the BMP280."""
    ID = 0xD0
    ID_VAL = 0x58  # Contents of the ID register for a BMP280.
    STATUS = 0xF3
    CONTROL = 0xF4
    CONFIG = 0xF5
    TEMP_MSB = 0xFA
    PRESS_MSB = 0xF7
    # Compensation coefficient registers.
    T1 = 0x88
    T2 = 0x8A
    T3 = 0x8C
    P1 = 0x8E
    P2 = 0x90
    P3 = 0x92
    P4 = 0x94
    P5 = 0x96
    P6 = 0x98
    P7 = 0x9A
    P8 = 0x9C
    P9 = 0x9E


class Bmp280base:
    """Base class for BMP280."""

    def __init__(self):
        """Create a Bmp280base instance. This is not meant to be instantiated directly.
        Use Bmp280spi or Bmp280i2c instead!"""
        self._temp = None
        self._press = None
        # Check if BMP280
        if self._readU8(Reg.ID) != Reg.ID_VAL:
            raise RuntimeError('Not a BMP280')
        # Read the compensation coefficients. I'm reading the coefficients
        # separately because reading them in one go failed.
        self._dig_T1 = float(self._readU16(Reg.T1))
        self._dig_T2 = float(self._readS16(Reg.T2))
        self._dig_T3 = float(self._readS16(Reg.T3))
        self._dig_P1 = float(self._readU16(Reg.P1))
        self._dig_P2 = float(self._readS16(Reg.P2))
        self._dig_P3 = float(self._readS16(Reg.P3))
        self._dig_P4 = float(self._readS16(Reg.P4))
        self._dig_P5 = float(self._readS16(Reg.P5))
        self._dig_P6 = float(self._readS16(Reg.P6))
        self._dig_P7 = float(self._readS16(Reg.P7))
        self._dig_P8 = float(self._readS16(Reg.P8))
        self._dig_P9 = float(self._readS16(Reg.P9))

    def _forcedmode(self):
        raise NotImplementedError

    def _readU8(self, register):
        """Read an unsigned byte from the specified register"""
        raise NotImplementedError

    def _readU16(self, register):
        """Read an unsigned short from the specified register"""
        raise NotImplementedError

    def _readS16(self, register):
        """Read an unsigned short from the specified register"""
        raise NotImplementedError

    def _readU24(self, register):
        """Read the 2.5 byte temperature or pressure registers."""
        raise NotImplementedError

    @property
    def comp(self):
        """Return the compensation coefficients as a dict."""
        return {
            'dig_T1': self._dig_T1,
            'dig_T2': self._dig_T2,
            'dig_T3': self._dig_T3,
            'dig_P1': self._dig_P1,
            'dig_P2': self._dig_P2,
            'dig_P3': self._dig_P3,
            'dig_P4': self._dig_P4,
            'dig_P5': self._dig_P5,
            'dig_P6': self._dig_P6,
            'dig_P7': self._dig_P7,
            'dig_P8': self._dig_P8,
            'dig_P9': self._dig_P9
        }

    @property
    def temperature(self):
        """The last measured temperature in °C."""
        return self._temp

    @property
    def pressure(self):
        """The last measured pressure in Pascal."""
        return self._press

    @property
    def mbar(self):
        """The last measured pressure in mbar"""
        return 1000 * (self._press / 1.013e2)

    def read(self):
        """Read the sensor data from the chip and return (temperature, pressure)."""
        # Do one measurement in high resolution, forced mode.
        self._forcedmode()
        # Wait while measurement is running
        while self._readU8(Reg.STATUS) & 0x08:
            sleep(0.01)
        # Now read and process the data.
        UT = self._readU24(Reg.TEMP_MSB)
        # print("DEBUG: UT = ", UT)
        var1 = (UT / 16384.0 - self._dig_T1 / 1024.0) * self._dig_T2
        # print("DEBUG: var1 = ", var1)
        var2 = ((UT / 131072.0 - self._dig_T1 / 8192.0) *
                (UT / 131072.0 - self._dig_T1 / 8192.0)) * self._dig_T3
        # print("DEBUG: var2 = ", var2)
        t_fine = int(var1 + var2)
        # print("DEBUG: t_fine = ", t_fine)
        self._temp = t_fine / 5120.0
        # print("DEBUG: self._temp = ", self._temp)
        # Read pressure.
        UP = self._readU24(Reg.PRESS_MSB)
        # print("DEBUG: UP = ", UP)
        var1 = t_fine / 2.0 - 64000.0
        # print("DEBUG: var1 = ", var1)
        var2 = var1 * var1 * self._dig_P6 / 32768.0
        # print("DEBUG: var2 = ", var2)
        var2 = var2 + var1 * self._dig_P5 * 2.0
        # print("DEBUG: var2 = ", var2)
        var2 = var2 / 4.0 + self._dig_P4 * 65536.0
        # print("DEBUG: var2 = ", var2)
        var1 = (self._dig_P3 * var1 * var1 / 534288.0 + self._dig_P2 * var1) / 534288.0
        # print("DEBUG: var1 = ", var1)
        var1 = (1.0 + var1 / 32768.0) * self._dig_P1
        # print("DEBUG: var1 = ", var1)
        if var1 == 0.0:
            return 0
        p = 1048576.0 - UP
        # print("DEBUG: p = ", p)
        p = ((p - var2 / 4096.0) * 6250) / var1
        # print("DEBUG: p = ", p)
        var1 = self._dig_P9 * p * p / 2147483648.0
        # print("DEBUG: var1 = ", var1)
        var2 = p * self._dig_P8 / 32768.0
        # print("DEBUG: var2 = ", var2)
        p = p + (var1 + var2 + self._dig_P7) / 16.0
        self._press = p
        # print("DEBUG: self._press = ", self._press)
        return (self._temp, self._press)


class Bmp280spi(Bmp280base):
    """Class to use a BMP280 over SPI."""

    def __init__(self, spi):
        """Create a Bmp280spi instance.

        Arguments:
            spi: SpiPort.

        >> from pyftdi.spi import SpiController
        >> from bmp280 import Bmp280spi
        >> ctrl = SpiController()
        >> ctrl.configure('ftdi://ftdi:232h/1')
        >> spi = ctrl.get_port(0)
        >> spi.set_frequency(1000000)
        >> bmp280 = Bmp280spi(spi)

        N.B: port 0 is pin D3 on the Adafruit FT232H. Only pins D3-D7 can be
        used as chip select! So you can connect at most 5 spi devices to the
        FT232H.
        """
        self._spi = spi
        super(Bmp280spi, self).__init__()

    def _forcedmode(self):
        """Set the sensor to forced mode."""
        self._spi.exchange([Reg.CONTROL & ~0x80, 0xFE])

    def _readU8(self, register):
        """Read an unsigned byte from the specified register"""
        return self._spi.exchange([register | 0x80], 1)[0]

    def _readU16(self, register):
        """Read an unsigned short from the specified register"""
        data = self._spi.exchange([register | 0x80], 2)
        return data[1] << 8 | data[0]

    def _readS16(self, register):
        """Read an unsigned short from the specified register"""
        result = self._readU16(register)
        if result > 32767:
            result -= 65536
        return result

    def _readU24(self, register):
        """Read the 2.5 byte temperature or pressure registers."""
        data = self._spi.exchange([register | 0x80], 3)
        rv = float((data[0] << 16 | data[1] << 8 | data[2]) >> 4)
        return rv


class Bmp280i2c(Bmp280base):
    """Class to use a BMP280 over I²C."""

    def __init__(self, i2c):
        """Create a Bmp280i2c instance.

        Arguments:
            i2c: i2cPort.

        >> from pyftdi.i2c import I2cController
        >> from bmp280 import Bmp280i2c
        >> ctrl = I2cController()
        >> ctrl.configure('ftdi://ftdi:232h/1')
        >> i2c = ctrl.get_port(0x77)
        >> bmp280 = Bmp280i2c(i2c)

        N.B: On the Adafruit breakout board, SDO is pulled high by default.
        So the default I²C address is 0x77. The port address will be 0x76
        if SDO is pulled low.
        """
        self._i2c = i2c
        super(Bmp280i2c, self).__init__()

    def _forcedmode(self):
        """Set the sensor to forced mode."""
        self._i2c.write_to(Reg.CONTROL, b'\xfe')

    def _readU8(self, register):
        """Read an unsigned byte from the specified register"""
        return self._i2c.read_from(register, 1)[0]

    def _readU16(self, register):
        """Read an unsigned short from the specified register"""
        data = self._i2c.read_from(register, 2)
        return data[1] << 8 | data[0]

    def _readS16(self, register):
        """Read an unsigned short from the specified register"""
        result = self._readU16(register)
        if result > 32767:
            result -= 65536
        return result

    def _readU24(self, register):
        """Read the 2.5 byte temperature or pressure registers."""
        data = self._i2c.read_from(register, 3)
        rv = float((data[0] << 16 | data[1] << 8 | data[2]) >> 4)
        return rv
