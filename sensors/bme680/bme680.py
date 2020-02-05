import time, datetime
import bme680
import json
#import requests
import sys
import logging, logging.handlers
import argparse

''' default variables values '''
#default_api_url = "https://dreamnetbe.net:4000/PiSense/sensor";
#sending_timeout = 2; # timeout used to wait a certain amount of time before returning the get/post of API
default_time = (15*60); # minutes calculated in seconds

''' arguments available to launch the app in a specific way '''
parser = argparse.ArgumentParser(prog='PiSense BME680', description='BME680 module sensor of PiSense', add_help=True, prefix_chars='-')
#parser.add_argument('-u', '--url', help='URL of the API', type=str, default=default_api_url, required=False)
parser.add_argument('-t', '--time', help='Time, in seconds, between each record taken', type=int, default=default_time, required=False)
parser.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.5')
args = parser.parse_args()

''' Log configuration '''
logger = logging.getLogger('bme680')
logger.setLevel(logging.INFO)
LOG_ROTATE = 'midnight'
# create a file handler and timed rotating
handler = logging.handlers.TimedRotatingFileHandler('bme680.log', when=LOG_ROTATE, backupCount=30, utc=False)
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

print('\n\nInitial reading:')
for name in dir(sensor.data):
    value = getattr(sensor.data, name)

    if not name.startswith('_'):
        print('{}: {}'.format(name, value))

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
                'temperature': sensor.data.temperature, # in Celsius
                'gas': '{0},{1}'.format(output, sensor.data.gas_resistance), # in ohm
                'humidity': sensor.data.humidity, # in percentage (%RH)
                'pressure': sensor.data.pressure} # in hectopascal
        data_json = json.dumps(charlie)
        logger.info('Records: %s', data_json)
    else:
        charlie = {'datetime': get_date_time(), # date T time in ISO8601
                'temperature': sensor.data.temperature, # in Celsius
                'gas': null,
                'humidity': sensor.data.humidity, # in percentage (%RH)
                'pressure': sensor.data.pressure} # in hectopascal
    # write JSON formatted data into specific file
#   with open('bme680data.json', 'a') as f:
#       f.write(data_json + "\n")
    return data_json

'''
# Fail method
def fail(msg):
    print(">>> Oops:",msg,file=sys.stderr)
    logger.warn('Oops: %s', msg)

def post_data():
    logger.info('Send data to server via API')
    try:
        r = requests.post(args.url, data=data, timeout=sending_timeout)
        logger.info('Status code: %s - %s', str(r.status_code), r.json()['message'])
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
'''

while True:
    try:
        now = datetime.datetime.now() # Get current date and time
        utc_offset_sec = time.altzone if time.localtime().tm_isdst else time.timezone
        utc_offset = datetime.timedelta(seconds=-utc_offset_sec)
        data = sensor_to_json()
#       post_data()
        time.sleep(args.time)
    except (KeyboardInterrupt, SystemExit):
        logger.info('KeyboardInterrupt/SystemExit caught')
        sys.exit()
