import time
import datetime
import digitalio
import board
import busio
import adafruit_bme280
import json
import requests
import sys
import logging
import logging.handlers
import argparse

''' default variables values '''
default_api_url = "https://s74.cwb.ovh/json.php";
sending_timeout = 2; # timeout used to wait a certain amount of time before returning the get/post of API
default_time = (10*60); # minutes calculated in seconds

''' arguments available to launch the app in a specific way '''
parser = argparse.ArgumentParser(prog='PiSense BME280', description='BME280 module sensor of PiSense', add_help=True, prefix_chars='-', allow_abbrev=True)
parser.add_argument('-u', '--url', help='URL of the API', type=str, default=default_api_url, required=False)
parser.add_argument('-t', '--time', help='Time, in seconds, between each record taken', type=int, default=default_time, required=False)
parser.add_argument('-v', '--version', help='%(prog)s program version', action='version', version='%(prog)s v0.6')
args = parser.parse_args()

''' Log configuration '''
logger = logging.getLogger('bme280')
logger.setLevel(logging.INFO)
LOG_ROTATE = 'midnight'
# create a file handler and timed rotating
handler = logging.handlers.TimedRotatingFileHandler('bme280.log', when=LOG_ROTATE, backupCount=30, utc=False)
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
#bme280 = adafruit_bme280.Adafruit_BME280_I2C(i2c)

# Create library object using Bus SPI port
spi = busio.SPI(board.SCK, board.MOSI, board.MISO)
bme_cs = digitalio.DigitalInOut(board.D5)
bme280 = adafruit_bme280.Adafruit_BME280_SPI(spi, bme_cs)

bme280.sea_level_pressure = 1013.25 # Change this to match the location's pressure (hPa) at sea level
bme280.mode = adafruit_bme280.MODE_NORMAL
bme280.standby_period = adafruit_bme280.STANDBY_TC_500
bme280.iir_filter = adafruit_bme280.IIR_FILTER_X16
bme280.overscan_pressure = adafruit_bme280.OVERSCAN_X16
bme280.overscan_humidity = adafruit_bme280.OVERSCAN_X1
bme280.overscan_temperature = adafruit_bme280.OVERSCAN_X2

''' Clear terminal and welcome process before starting everything '''
print(chr(27) + "[2J")
print('Welcome to PiSense')
print('==================')
print('Altitude is calculated based on pressure information.')
logger.info('Start record of BME280 sensor')
logger.info('Altitude will not be shown nor used.')
print('-------------------')

# The sensor will need a moment to gather initial readings
time.sleep(1)

''' ISO8601 format date and time'''
def get_date_time():
    return now.replace(tzinfo=datetime.timezone(offset=utc_offset)).isoformat()

''' JSON part '''
## {"datetime": "2019-11-14T10:11:59.378308+01:00", "temperature": 22.6, "humidity": 37.3, "pressure": 986.74}
def sensor_to_json():
    # dict which will be used by JSON
    bob = {'datetime': get_date_time(), # date T time in ISO8601
            'temperature': float(f'{bme280.temperature:.1f}'), # in Celsius
            'humidity': float(f'{bme280.humidity:.1f}'), # in percentage
            'pressure': float(f'{bme280.pressure:.2f}')} # in hectopascal
    #date_json=bob
    # This part is for debug mode only because when used, it stop the sending of JSON to API
    # data_json = json.dumps(bob)

    logger.info('Records: %s', bob)
    # write JSON formatted data into specific file
#   with open('bme280data.json', 'a') as f:
#       f.write(data_json + "\n")
    return bob

''' Fail method '''
def fail(msg):
    print(">>> Oops:",msg,file=sys.stderr)
    logger.warn('Oops: %s', msg)

def post_data(datas):
    logger.info('Sending data to server via API...')
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

while True:
    try:
        now = datetime.datetime.now() # Get current date and time
        utc_offset_sec = time.altzone if time.localtime().tm_isdst else time.timezone
        utc_offset = datetime.timedelta(seconds=-utc_offset_sec)
        data = sensor_to_json()
        
        post_data(data)
        time.sleep(args.time)
    except (KeyboardInterrupt, SystemExit):
        logger.info('KeyboardInterrupt/SystemExit caught')
        sys.exit()
