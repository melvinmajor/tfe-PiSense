import time
import datetime
import board
import digitalio # For use with SPI
import busio
import adafruit_bmp280
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
feature = argparse.ArgumentParser(prog='PiSense BMP280', add_help=True, prefix_chars='-', allow_abbrev=True, formatter_class=argparse.RawTextHelpFormatter, description=textwrap.dedent('''\
        PiSense - BMP280 module
        -----------------------
        This script is meant to be used with the Adafruit BMP280 sensor.
        Environmental values will be taken and sent throughout an API or directly written in a localhost website.
        The environmental values are: temperature and humidity.
    '''))
feature.add_argument('-u', '--url', help='URL of the API', type=str, default=default_api_url, required=False)
feature.add_argument('-t', '--time', help='Time, in seconds, between each record taken', type=int, default=default_time, required=False)
feature.add_argument('-a', '--api', help='Sets API usage in activated state. Use this if you want to use API version (localhost will still run)', action='store_true', default=api_usage, required=False)
feature.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.8.2')
args = feature.parse_args()

''' Log configuration '''
logger = logging.getLogger('bmp280')
logger.setLevel(logging.INFO)
LOG_ROTATE = 'midnight'
# create a file handler and timed rotating
handler = logging.handlers.TimedRotatingFileHandler('bmp280.log', when=LOG_ROTATE, backupCount=7, utc=False) # 7 days backup
handler.setLevel(logging.INFO)
# create a logging format
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s', '%Y-%m-%d %H:%M:%S')
handler.setFormatter(formatter)
# add the handlers to the logger
logger.addHandler(handler)
alice = logging.StreamHandler()
alice.setFormatter(formatter)
logger.addHandler(alice)

''' Create library object using Bus I2C or SPI port '''
# Create library object using Bus I2C port
#i2c = busio.I2C(board.SCL, board.SDA)
#bmp280 = adafruit_bmp280.Adafruit_BMP280_I2C(i2c)

# Create library object using Bus SPI port
spi = busio.SPI(board.SCK, board.MOSI, board.MISO)
bmp_cs = digitalio.DigitalInOut(board.D5)
bmp280 = adafruit_bmp280.Adafruit_BMP280_SPI(spi, bmp_cs)

bmp280.sea_level_pressure = 1013.25 # Change this to match the location's pressure (hPa) at sea level

''' Clear terminal and welcome process before starting everything '''
print(chr(27) + "[2J")
print('Welcome to PiSense')
print('==================')
print('Altitude is calculated based on pressure information.')
logger.info('Start record of BMP280 sensor')
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
## {"datetime": "2019-11-14T10:11:59.378308+01:00", "temperature": 22.6, "pressure": 986.74}
def sensor_to_json():
    # dict which will be used by JSON
    dan = {'datetime': get_date_time(), # date T time in ISO8601
            'temperature': float(f'{bmp280.temperature:.1f}'), # in Celsius
            'pressure': float(f'{bmp280.pressure:.2f}')} # in hectopascal
    # This part is for debug mode only because when used, it stop the sending of JSON to API
    # data_json = json.dumps(dan)
    logger.info('Records: %s', dan)

    # write JSON formatted data into specific file
    # with open('bmp280data.json', 'a') as f:
    #    f.write(data_json + "\n")
    return dan

''' Fail method '''
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
    dan = json.dumps(data)
    dan = "[" + dan + "]"
    try:
        with open(JSON_FILE, 'w') as f:
            f.write(dan)
            logger.info('Data recorded successfully')
    except IOError as e:
        fail('IOError while trying to open and write JSON file')
   
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
            fail("Please use `python3 bmp280sensor.py` or `python3 bmp280sensor.py --api` in order to choose between localhost or API + localhost version...")
        time.sleep(args.time)

    except (KeyboardInterrupt, SystemExit):
        logger.info('KeyboardInterrupt/SystemExit caught')
        sys.exit()
