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
try:
    import configparser
except:
    from six.moves import configparser

config = configparser.ConfigParser()
config.read('config.ini')

''' default variables values '''
default_api_url = config['default']['default_api_url']
sending_timeout = int(config['default']['sending_timeout']) # timeout used to wait a certain amount of time before returning the get/post of API
default_time = int(config['environment']['default_time'])
localhost_usage = config.getboolean('default', 'localhost_usage')
api_usage = config.getboolean('default', 'api_usage')
JSON_FILE = config['environment']['json_file']
# Variables for rich notification
EVENT_NAME = config['notification']['eventName']
KEY = config['notification']['key']
PISENSE_ALERT_NOTIFICATION = config['notification']['pisense_alert_notification']
LOW_TEMPERATURE_CHECKUP = config.getfloat('environment', 'low_temperature')
HIGH_TEMPERATURE_CHECKUP = config.getfloat('environment', 'high_temperature')

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
feature.add_argument('-a', '--api', help='Sets API usage in activated state. Use this if you want to use API version (localhost will still run)', action='store_true', default=api_usage, required=False)
feature.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.9.1')
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

if args.api:
    api_usage = True;
    logger.info('User asked to use API!')
else:
    api_usage = False;
    logger.info('Running localhost only version')

if args.url:
    default_api_url = args.url
    logger.info('API URL set up: %s', args.url)

if args.time:
    default_time = args.time
    logger.info('Time between records set up: %s seconds', args.time)

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
    return now.replace(tzinfo=datetime.timezone(offset=utc_offset)).isoformat()

''' JSON part '''
## {"datetime": "2019-11-14T10:11:59.378308+01:00", "temperature": 32.3, "gas": 11671, "humidity": 43.9, "pressure": 1017.28}
def sensor_to_json():
    # dict which will be used by JSON
    # - 1°C hardcoded because digital calibration has 1°C higher value than reality
    if sensor.get_sensor_data():
        if sensor.data.heat_stable:
            charlie = {'datetime': get_date_time(), # date T time in ISO8601
                'temperature': float(f'{sensor.data.temperature:.1f}') - 1, # in Celsius
                'gas': float(f'{sensor.data.gas_resistance:.2f}'), # in ohm
                'humidity': float(f'{sensor.data.humidity:.1f}'), # in percentage (%RH)
                'pressure': float(f'{sensor.data.pressure:.2f}')} # in hectopascal
            # This part is for debug mode only because when used, it stop the sending of JSON to API
            # data_json_stable = json.dumps(charlie)
            # logger.info('Records: %s', charlie)
            return charlie
    else:
        charlie = {'datetime': get_date_time(), # date T time in ISO8601
                'temperature': float(f'{sensor.data.temperature:.1f}') - 1, # in Celsius
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
    logger.warning('Oops: %s', msg)

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

def notification(dataType, info):
    base_url = 'https://maker.ifttt.com/trigger/{}/with/key/{}'
    url = base_url.format(EVENT_NAME, KEY)
    report = {}
    report["value1"] = dataType
    report["value2"] = str(info) + "°C"
    report["value3"] = PISENSE_ALERT_NOTIFICATION
    requests.post(url, data=report)


while True:
    try:
        now = datetime.datetime.now() # Get current date and time
        utc_offset_sec = time.altzone if time.localtime().tm_isdst else time.timezone
        utc_offset = datetime.timedelta(seconds=-utc_offset_sec)
        
        data = sensor_to_json()
        # Check if API parameter is used in order to use both localhost and API version or not
        if(api_usage == True):
            local_data(data)
            post_data(data)
        elif(localhost_usage == True):
            local_data(data)
        else:
            fail("Please use `python3 bme680sensor.py` or `python3 bme680sensor.py --api` in order to choose between localhost only or API + localhost version...")

        temperature = float(f'{sensor.data.temperature:.1f}')

        if(temperature <= LOW_TEMPERATURE_CHECKUP):
            dataType = 'low temperature'
            info = temperature
            notification(dataType, info)
            logger.info('Rich notification sent to IFTTT, %s reached %s', dataType, temperature)
        elif(temperature >= HIGH_TEMPERATURE_CHECKUP):
            dataType = 'high temperature'
            info = temperature
            notification(dataType, info)
            logger.info('Rich notification sent to IFTTT, %s reached %s', dataType, temperature)

        time.sleep(args.time)

    except (KeyboardInterrupt, SystemExit):
        logger.info('KeyboardInterrupt/SystemExit caught')
        sys.exit()

