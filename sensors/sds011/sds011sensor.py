#!/usr/bin/python -u
# coding=utf-8
# "DATASHEET": http://cl.ly/ekot
# Original code (used as library for this development as it's the only working code): https://gist.github.com/kadamski/92653913a53baf9dd1a8
from __future__ import print_function
from datetime import datetime
import serial
import struct
import sys
import time
import datetime
import json
import subprocess
import requests
import logging
import logging.handlers
import argparse
import textwrap

''' default variables values '''
default_api_url = "https://s74.cwb.ovh/json.php";
sending_timeout = 2; # timeout used to wait a certain amount of time before returning the get/post of API
default_time = (15*60); # minutes calculated in seconds
localhost_usage = True;
api_usage = False;
JSON_FILE = '/var/www/html/assets/aqi.json';

# This part is for SDS011 sensor and simple intranet usage
DEBUG = 0
CMD_MODE = 2
CMD_QUERY_DATA = 4
CMD_DEVICE_ID = 5
CMD_SLEEP = 6
CMD_FIRMWARE = 7
CMD_WORKING_PERIOD = 8
MODE_ACTIVE = 0
MODE_QUERY = 1
PERIOD_CONTINUOUS = 0
JSON_FILE = '/var/www/html/aqi.json'
MQTT_HOST = ''
MQTT_TOPIC = '/weather/particulatematter'

''' arguments available to launch the app in a specific way '''
feature = argparse.ArgumentParser(prog='PiSense SDS011', add_help=True, prefix_chars='-', formatter_class=argparse.RawTextHelpFormatter, description=textwrap.dedent('''\
    PiSense - SDS011 module
    -----------------------
    This script is meant to be used with the Nova SDS011 sensor.
    AQI values will be taken and sent throughout an API or directly written in a localhost website.
    The AQI values are: PM2.5 and PM10.
    '''))
feature.add_argument('-u', '--url', help='URL of the API', type=str, default=default_api_url, required=False)
feature.add_argument('-t', '--time', help='Time, in seconds, between each record taken', type=int, default=default_time, required=False)
feature.add_argument('-a', '--api', help='Sets API usage in activated state. Use this if you want to use API version (localhost will still run)', action='store_true', default=api_usage, required=False)
feature.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.8')
args = feature.parse_args()

if args.api:
    api_usage = True;
else:
    api_usage = False;

''' Log configuration '''
logger = logging.getLogger('sds011')
logger.setLevel(logging.INFO)
LOG_ROTATE = 'midnight'
# create a file handler and timed rotating
handler = logging.handlers.TimedRotatingFileHandler('sds011.log', when=LOG_ROTATE, backupCount=7, utc=False) # 7 days backup
handler.setLevel(logging.INFO)
# create a logging format
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s', '%Y-%m-%d %H:%M:%S')
handler.setFormatter(formatter)
# add the handlers to the logger
logger.addHandler(handler)
alice = logging.StreamHandler()
alice.setFormatter(formatter)
logger.addHandler(alice)

''' Clear terminal and welcome process before starting everything '''
print(chr(27) + "[2J")
print('Welcome to PiSense')
print('==================')
logger.info('Start record of SDS011 sensor')
print('-------------------')

# The sensor will need a moment to gather initial readings
time.sleep(2)

''' Method which may be used to check if parameter used has a value that can be considered as boolean '''
def str2bool(v):
    if isinstance(v, bool):
        return v
    if v.lower() in ('yes', 'true', 't', 'y', '1'):
        return True
    elif v.lower() in ('no', 'false', 'f', 'n', '0'):
        return False
    else:
        raise argparse.ArgumentTypeError('Boolean value expected.')

''' ISO8601 format date and time'''
def get_date_time():
#    return now.replace(tzinfo=datetime.timezone(offset=utc_offset)).isoformat()
     return datetime.now().strftime('%Y-%m-%dT%H:%M:%S:%SZ')

ser = serial.Serial()
ser.port = "/dev/ttyUSB0"
ser.baudrate = 9600

ser.open()
ser.flushInput()

byte, data = 0, ""

def dump(d, prefix=''):
    logger.info(prefix + ' '.join(x.encode('hex') for x in d))

def construct_command(cmd, data=[]):
    assert len(data) <= 12
    data += [0,]*(12-len(data))
    checksum = (sum(data)+cmd-2)%256
    ret = "\xaa\xb4" + chr(cmd)
    ret += ''.join(chr(x) for x in data)
    ret += "\xff\xff" + chr(checksum) + "\xab"

    if DEBUG:
        dump(ret, '> ')
    return ret

def process_data(d):
    r = struct.unpack('<HHxxBB', d[2:])
    pm25 = r[0]/10.0
    pm10 = r[1]/10.0
    checksum = sum(ord(v) for v in d[2:8])%256
    return [pm25, pm10]
    #print("PM 2.5: {} μg/m^3  PM 10: {} μg/m^3 CRC={}".format(pm25, pm10, "OK" if (checksum==r[2] and r[3]==0xab) else "NOK"))

def process_version(d):
    r = struct.unpack('<BBBHBB', d[3:])
    checksum = sum(ord(v) for v in d[2:8])%256
    logger.info("Y: {}, M: {}, D: {}, ID: {}, CRC={}".format(r[0], r[1], r[2], hex(r[3]), "OK" if (checksum==r[4] and r[5]==0xab) else "NOK"))

def read_response():
    byte = 0
    while byte != "\xaa":
        byte = ser.read(size=1)

    d = ser.read(size=9)

    if DEBUG:
        dump(d, '< ')
    return byte + d

def cmd_set_mode(mode=MODE_QUERY):
    ser.write(construct_command(CMD_MODE, [0x1, mode]))
    read_response()

def cmd_query_data():
    ser.write(construct_command(CMD_QUERY_DATA))
    d = read_response()
    values = []
    if d[1] == "\xc0":
        values = process_data(d)
    return values

def cmd_set_sleep(sleep):
    mode = 0 if sleep else 1
    ser.write(construct_command(CMD_SLEEP, [0x1, mode]))
    read_response()

def cmd_set_working_period(period):
    ser.write(construct_command(CMD_WORKING_PERIOD, [0x1, period]))
    read_response()

def cmd_firmware_ver():
    ser.write(construct_command(CMD_FIRMWARE))
    d = read_response()
    process_version(d)

def cmd_set_id(id):
    id_h = (id>>8) % 256
    id_l = id % 256
    ser.write(construct_command(CMD_DEVICE_ID, [0]*10+[id_l, id_h]))
    read_response()

def pub_mqtt(jsonrow):
    cmd = ['mosquitto_pub', '-h', MQTT_HOST, '-t', MQTT_TOPIC, '-s']
    logger.info('Publishing using: ', cmd)
    with subprocess.Popen(cmd, shell=False, bufsize=0, stdin=subprocess.PIPE).stdin as f:
        json.dump(jsonrow, f)


''' JSON part '''
## {"datetime": "2020-05-24T15:08:05.274635+02:00", "pm25": 2.3, "pm10": 4.9}
def sensor_to_json():
    # dict which will be used by JSON

    dave = {'datetime': datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S'), # date T time in ISO8601
            'PM2': values[0], # PM2.5 in µg/m3
            'PM10': values[1]} # PM10 in µg/m3
    #date_json=dave
    # This part is for debug mode only because when used, it stop the sending of JSON to API
    # data_json = json.dumps(dave)
    logger.info('Records: %s', dave)

    # write JSON formatted data into specific file
    # with open('bme280data.json', 'a') as f:
    #    f.write(data_json + "\n")
    return dave

''' Fail method '''
def fail(msg):
    print(">>> Oops: ",msg,file=sys.stderr)
    logger.warning('Oops: %s', msg)

def post_data(datas):
    logger.info('Sending data to server via API...')
    sensor_to_json()
    try:
        #print('debug')
        #print(datas)
        #print(args.url)
        r = requests.post(args.url,data=datas)
        # To see what the online server answer:        print(r.text)
        #logger.info('Status code: %s - %s', str(r.status_code), r.json()['message'])
        if r.status_code in range(200,300):
            logger.info('Success')
        else:
            fail(str(r.status_code))
    except requests.exceptions.HTTPError as err:
        fail('HTTP error')
    except requests.exceptions.ConnectionError as errc:
        fail('Connection error')
    except requests.exceptions.Timeout as errt:
        # set up for a tmp file before next try
        fail('Timeout error')
    except requests.exceptions.RequestException as e:
        # catastrophic error, you need to go to jail
        fail('Request error')

local_data(datas):
    logger.info('Writing data to localhost webserver...')
    sensor_to_json()
    # open stored data
    try:
        with open(JSON_FILE) as json_data:
            data = json.load(json_data)
    except IOError as e:
        data = []
        fail('IOError while trying to open JSON file')
    # check if length is more than 100 and delete first element
    if len(data) > 100:
        data.pop(0)
    # append new values
    jsonrow = {'pm25': values[0], 'pm10': values[1], 'time': get_date_time()} # time.strftime("%d.%m.%Y %H:%M:%S")
    data.append(jsonrow)
    # save it
    with open(JSON_FILE, 'w') as outfile:
        json.dump(data, outfile)
        logger.info('Data recorded successfully')
    if MQTT_HOST != '':
        pub_mqtt(jsonrow)

if __name__ == "__main__":
    cmd_set_sleep(0)
    #print('1')
    cmd_set_mode(1)
    cmd_firmware_ver()
    #print('2')
    cmd_set_working_period(PERIOD_CONTINUOUS)
    cmd_set_mode(MODE_QUERY);
    while True:
        try:
            cmd_set_sleep(0)
            
            now = datetime.datetime.now() # Get current date and time
            utc_offset_sec = time.altzone if time.localtime().tm_isdst else time.timezone
            utc_offset = datetime.timedelta(seconds=-utc_offset_sec)

            for t in range(15):
                values = cmd_query_data();
                if values is not None and len(values) == 2:
                  print("PM2.5: ", values[0], ", PM10: ", values[1])
                  time.sleep(2)

            data = sensor_to_json()
            # Check if API parameter is used in order to use both localhost and API version or not
            if(api_usage == True):
                local_data(data)
                post_data(data)
            elif(localhost_usage == True):
                local_data(data)
            else:
                fail("Please use `python3 sds011sensor.py` or `python3 sds011sensor.py --api` in order to choose between localhost or API + localhost version...")

            #print("Going to sleep for 15 min...")
            cmd_set_sleep(1)
            #time.sleep(900)
            time.sleep(args.time)

        except (KeyboardInterrupt, SystemExit):
            logger.info('KeyboardInterrupt/SystemExit caught')
            sys.exit()
