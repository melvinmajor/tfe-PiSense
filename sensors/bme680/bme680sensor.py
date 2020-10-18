import time
import datetime
import bme680
import json
import requests
import sys
import logging
import logging.handlers
import argparse
import textwrap

''' default variables values '''
default_api_url = "https://s74.cwb.ovh/json.php";
sending_timeout = 2; # timeout used to wait a certain amount of time before returning the get/post of API
default_time = (5*60); # minutes calculated in seconds
localhost_usage = True;
api_usage = False;
JSON_FILE = '/var/www/html/assets/environment.json';

''' arguments available to launch the app in a specific way '''
feature = argparse.ArgumentParser(prog='PiSense BME680', add_help=True, prefix_chars='-', formatter_class=argparse.RawTextHelpFormatter, description=textwrap.dedent('''\
        PiSense - BME680 module
        -----------------------
        This script is meant to be used with the Pimoroni BME680 sensor.
        Environmental values will be taken and sent throughout an API or directly written in a localhost website.
        The environmental values are: temperature, humidity, atmospheric pressure and gas.
        '''))
feature.add_argument('-u', '--url', help='URL of the API', type=str, default=default_api_url, required=False)
feature.add_argument('-t', '--time', help='Time, in seconds, between each record taken', type=int, default=default_time, required=False)
feature.add_argument('-a', '--api', help='Sets API usage in activated state. Use this if you want to use API version.', action='store_true', default=api_usage, required=False)
feature.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.7')
args = feature.parse_args()

''' Log configuration '''
logger = logging.getLogger('bme680')
logger.setLevel(logging.INFO)
LOG_ROTATE = 'midnight'
# create a file handler and timed rotating
handler = logging.handlers.TimedRotatingFileHandler('bme680.log', when=LOG_ROTATE, backupCount=7, utc=False) # 7 days backup
handler.setLevel(logging.INFO)
# create a logging format
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s', '%Y-%m-%d %H:%M:%S')
handler.setFormatter(formatter)
# add the handlers to the logger
logger.addHandler(handler)
alice = logging.StreamHandler()
alice.setFormatter(formatter)
logger.addHandler(alice)

''' Calibration and initial reading '''
try:
    sensor = bme680.BME680(bme680.I2C_ADDR_PRIMARY)
except IOError:
    sensor = bme680.BME680(bme680.I2C_ADDR_SECONDARY)

# These calibration data can safely be commented out, if desired.
print('Calibration data:')
for name in dir(sensor.calibration_data):
    if not name.startswith('_'):
        value = getattr(sensor.calibration_data, name)
        if isinstance(value, int):
            print('{}: {}'.format(name, value))

# These oversampling settings can be tweaked to
# change the balance between accuracy and noise in
# the data.
sensor.set_humidity_oversample(bme680.OS_2X)
sensor.set_pressure_oversample(bme680.OS_4X)
sensor.set_temperature_oversample(bme680.OS_8X)
sensor.set_filter(bme680.FILTER_SIZE_3)
sensor.set_gas_status(bme680.ENABLE_GAS_MEAS)

sensor.set_gas_heater_temperature(320)
sensor.set_gas_heater_duration(150)
sensor.select_gas_heater_profile(0)

# Up to 10 heater profiles can be configured, each
# with their own temperature and duration.
# sensor.set_gas_heater_profile(200, 150, nb_profile=1)
# sensor.select_gas_heater_profile(1)

''' Clear terminal and welcome process before starting everything '''
print(chr(27) + "[2J")
print('Welcome to PiSense')
print('==================')
print('Altitude is calculated based on pressure information.')
logger.info('Start record of BME680 sensor')
logger.info('Altitude will not be shown nor used.')
print('-------------------')

# The sensor will need a moment to gather initial readings
time.sleep(2)

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
    return now.replace(tzinfo=datetime.timezone(offset=utc_offset)).isoformat()

''' JSON part '''
## {"datetime": "2019-11-14T10:11:59.378308+01:00", "temperature": 32.3, "gas": 11671, "humidity": 43.9, "pressure": 1017.28}
def sensor_to_json():
    # dict which will be used by JSON
    if sensor.get_sensor_data():
        if sensor.data.heat_stable:
            charlie = {'datetime': get_date_time(), # date T time in ISO8601
                'temperature': float(f'{sensor.data.temperature:.1f}'), # in Celsius
                'gas': float(f'{sensor.data.gas_resistance:.2f}'), # in ohm
                'humidity': float(f'{sensor.data.humidity:.1f}'), # in percentage (%RH)
                'pressure': float(f'{sensor.data.pressure:.2f}')} # in hectopascal
            # This part is for debug mode only because when used, it stop the sending of JSON to API
            # data_json_stable = json.dumps(charlie)
            # logger.info('Records: %s', charlie)
            return charlie
    else:
        charlie = {'datetime': get_date_time(), # date T time in ISO8601
                'temperature': float(f'{sensor.data.temperature:.1f}'), # in Celsius
            'gas': None,
            'humidity': float(f'{sensor.data.humidity:.1f}'), # in percentage (%RH)
            'pressure': float(f'{sensor.data.pressure:.2f}')} # in hectopascal
        return charlie
        # This part is for debug mode only because when used, it stop the sending of JSON to API
        # data_json = json.dumps(charlie)
        logger.info('Records: %s', charlie)

    # write JSON formatted data into specific file
    # with open('bme680data.json', 'a') as f:
    #     f.write(data_json + "\n")

# Fail method
def fail(msg):
    print(">>> Oops:",msg,file=sys.stderr)
    logger.warn('Oops: %s', msg)

def post_data(datas):
    logger.info('Sending data to server via API...')
    sensor_to_json()
    try:
        #print('debug')
        #print(datas)
        #print(args.url)
        r = requests.post(args.url, data=data, timeout=sending_timeout)
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

def local_data(datas):
    logger.info('Writing data to localhost webserver...')
    sensor_to_json()
    # convert dictionary into string
    charlie = json.dumps(data)
    charlie = "[" + charlie + "]"
    try:
        with open(JSON_FILE, 'w') as f:
            f.write(charlie)
            logger.info('Data recorded successfully')
    except IOError as e:
        fail('IOError while trying to open and write JSON file')

# Open stored data
#    try:
#        with open(JSON_FILE) as json_data:
#            data = json.load(json_data)
#    except IOError as e:
#        data = []
#        fail('IOError while trying to open and load JSON file')
    # Check if length is more than 100 and delete first element
#    if len(data) > 100:
#        data.pop(0)
    # Save data
#    try:
#        with open(JSON_FILE, 'w') as outfile:
#            json.dumps(data, outfile)
#            logger.info('Records: %s', data)
#    except IOError as e:
#        fail('IOError while trying to save data into JSON file')

while True:
    try:
        now = datetime.datetime.now() # Get current date and time
        utc_offset_sec = time.altzone if time.localtime().tm_isdst else time.timezone
        utc_offset = datetime.timedelta(seconds=-utc_offset_sec)
        
        data = sensor_to_json()
        # Check if API parameter is used in order to use both localhost and API version or not
        if(localhost_usage == True):
            local_data(data)
        elif(api_usage == True):
            local_data(data)
            post_data(data)
        else:
            fail("Please use `python3 bme680sensor.py` or `python3 bme680sensor.py -lh -api` in order to choose between localhost or API version...")
        time.sleep(args.time)

    except (KeyboardInterrupt, SystemExit):
        logger.info('KeyboardInterrupt/SystemExit caught')
        sys.exit()
