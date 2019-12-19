#!/usr/bin/python
# -*- coding: utf-8 -*-
'''
Copyright 2016, Frank Heuer, Germany
test.py is demonstrating some capabilities of the SDS011 module.
If you run this using your Nova Fitness Sensor SDS011 and
do not get any error (one warning will be ok) all is fine.

This is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

GitLab repository: https://gitlab.com/frankrich/sds011_particle_sensor
'''
import sys
import time
from sds011 import SDS011

# Create a new sensor instance

'''
On Win, the path is one of the com ports. On Linux / Raspberry Pi
it depends. May be one of "/dev/ttyUSB..." or "/dev/ttyAMA...".
On the Pi make sure the login getty() is not using the serial interface.
Have a look at Win or Linux documentation.
Look e.g. via lsusb command for Qin Hen Electronics USB id.
'''

def printlog(level, string):
    """Change this to reflect the way logging is done."""
    sys.stderr.write("%s: %s\n" % (level, string))

debug = 0       # debug level in sds011 class module
cycles = 4      # serial read timeout in seconds, dflt 2
timeout = 2     # timeout on serial line read
# print values in mass or pieces
unit_of_measure = SDS011.UnitsOfMeasure.MassConcentrationEuropean
for arg in range(len(sys.argv) - 1, 0, -1):
    if sys.argv[arg][0:2] == '-d':
        debug += 1
        debug = int(sys.argv[arg + 1])
        del sys.argv[arg + 1]
        del sys.argv[arg]
    elif sys.argv[arg][0:2] == '-c':
        cycles = int(sys.argv[arg + 1])
        del sys.argv[arg + 1]
        del sys.argv[arg]
    elif sys.argv[arg][0:2] == '-t':
        timeout = int(sys.argv[arg + 1])
        del sys.argv[arg + 1]
        del sys.argv[arg]
    elif sys.argv[arg][0:2] == '-u':
        if sys.argv[arg + 1] == '0':
            unit_of_measure = SDS011.UnitsOfMeasure.MassConcentrationEuropean
        elif sys.argv[arg + 1] == '1':
            unit_of_measure = SDS011.UnitsOfMeasure.ParticelConcentrationImperial
        else:
            raise RuntimeError("%s is not a valid unit of measure")
        del sys.argv[arg + 1]
        del sys.argv[arg]
print ('Argument List:', str(sys.argv))
if len(sys.argv) < 2:
    sys.exit("Usage: python test.py [-d {1..5}] [-c cnt] [-t secs] [-u 0|1] com_port [duty_cycle 0..30]\n"
             "com port e.g. /dev/ttyUSB0\n"
             "-d(ebug) debug level (dflt 0). Use 10, 14, 16, 18, 20, 30, 40, 50. Low value means verbose, 0 means off\n"
             "-c(nt) cnt defines the amount of test cycles (dflt 4).\n"
             "-t(imeout) secs defines the timeout of serial line readings (dflt 2).\n"
             "-u(nit_of_measure):\n\
             \t0: output in µg/m3 (default);\n\
             \t1: output in pcs/0.01qf (pieces per cubic feet)\n"
             "\t\tduty cycle defines sensor measurement time in minutes (dflt 2).")
if debug > 0:
    # Activate simple logging
    import logging
    import logging.handlers
    logger = logging.getLogger()
    # Available levels are the well known
    # logging.INFO, logging.WARNING and so forth.
    # Between INFO (=20)and DEBUG (=10) are fine grained
    # messages with levels 14,16 and 18. You might want
    # to use these values. Here is an Example with 16
    # logger.setLevel(16)
    # Activate simple logging
    logger.setLevel(debug)

def printValues(timing, values, unit_of_measure):
    if unit_of_measure == SDS011.UnitsOfMeasure.MassConcentrationEuropean:
        unit = 'µg/m³'
    else:
        unit = 'pcs/0.01cft'
    print("Waited %d secs\nValues measured in %s:    PM2.5  " %
          (timing, unit), values[1], ", PM10 ", values[0])
    # print("Values measured in pcs/0.01sqf: PM2.5 %d, PM10 %d" % (Mass2Con('pm25',values[1]), Mass2Con('pm10',values[0])))

# simple parsing the command arguments for setting options
# Create an instance of your sensor
# options defaults: logging None, debug level 0, serial line timeout 2
# option unit_of_measure (default False) values in pcs/0.01sqf or mass ug/m3
sensor = SDS011(sys.argv[1], timeout=timeout, unit_of_measure=unit_of_measure)
# raise KeyboardInterrupt
# Now we have some details about it
print("SDS011 sensor info:")
print("Device ID: ", sensor.device_id)
print("Device firmware: ", sensor.firmware)
print("Current device cycle (0 is permanent on): ", sensor.dutycycle)
print(sensor.workstate)
print(sensor.reportmode)

print("\n%d measurements in permanent measuring mode" % (cycles * 2))
print("This will make the sensor getting old. The TTL is just 8000 hours!")
print("Do you really need to use the permanent measurering mode?")
print("In sleep mode the fan will be turned off.")
# Set dutycyle to nocycle (permanent)
sensor.reset()
for a in range(2 * cycles):
    while True:
        values = sensor.get_values()
        if values is not None:
            printValues(0, values, sensor.unit_of_measure)
            break

try:
    # Example of switching the WorkState
    print("\n%d X switching between measuring and sleeping mode:" % cycles)
    print("\tMeasurement state: Read the values, on no read, wait 2 seconds and try again")
    print("\tOn read success, put the mode into sleeping mode for 5 seconds, and loop again")
    for a in range(cycles):
        print("%d time: push it into wake state" % a)
        sensor.workstate = SDS011.WorkStates.Measuring
        # Just to demonstrate. Should be 60 seconds to get qualified values.
        # The sensor needs to warm up!
        time.sleep(10)
        last = time.time()
        while True:
            last1 = time.time()
            values = sensor.get_values()
            if values is not None:
                printValues(time.time() - last, values, sensor.unit_of_measure)
                break
            print("Waited %d seconds, no values read, wait 2 seconds, and try to read again" % (
                time.time() - last1))
            time.sleep(2)
        print("Read was succesfull. Going to sleep for 5 seconds")
        sensor.workstate = SDS011.WorkStates.Sleeping
        time.sleep(5)

    # Example of duty cycle
    DC = 2
    if len(sys.argv) > 2 and sys.argv[2].isdigit():
        if int(sys.argv[2]) >= 0 and int(sys.argv[2]) < 30:
            DC = int(sys.argv[2])
        else:
            print("Invalid duty cycle %s. Using 2 minutes" % sys.argv[2])
    print("\n%d X measurements with duty cycle of  %d minutes." % (cycles, DC))
    #sensor.workstate = SDS011.WorkStates.Measuring
    # Setting this to 0 means permanent measurement for once each second
    # sensor.dutycycle = DC  # valid values between 0 and 30
    print("Waiting time: at most {0} minutes before the measurement.".format(
        sensor.dutycycle))
    for a in range(cycles):
        print("Duty cycle measurement nr {0}.".format(a))
        sensor.workstate = SDS011.WorkStates.Measuring
        sensor.dutycycle = DC  # valid values between 0 and 30
        last = time.time()
        while True:
            last1 = time.time()
            values = sensor.get_values()
            if values is not None:
                printValues(time.time() - last, values, sensor.unit_of_measure)
                break
            print("Waited %d secs, no values read, we try again" %
                  (time.time() - last1))
        sensor.workstate = SDS011.WorkStates.Sleeping
        print("Read was succesfull. Going to sleep for 5 seconds")
        time.sleep(10)

    # end of test
    print("\nSensor reset to normal")
    sensor.reset()
    sensor = None
except KeyboardInterrupt:
    sensor.reset()
    sensor = None
    sys.exit("Sensor reset due to a KeyboardInterrupt")

print("Finished test")
