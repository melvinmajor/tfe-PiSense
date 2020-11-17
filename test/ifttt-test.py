import requests
try:
    import configparser
except:
    from six.moves import configparser

config = configparser.ConfigParser()
config.read('../sensors/config.ini')

# notify
def notification(eventName, key, data, value, pisense):
    base_url = 'https://maker.ifttt.com/trigger/{}/with/key/{}'
    url = base_url.format(eventName, key)
    report = {}
    report["value1"] = data
    report["value2"] = value
    report["value3"] = pisense
    requests.post(url, data=report)

#print("What's the EventName of the IFTTT webhook?")
#eventName = input()
eventName = config['notification']['eventName']
#print("What's the key linked to the IFTTT webhook?")
#key = input()
key = config['notification']['key']
print("Choose the type of data:")
a = input()
print("Choose the value of the data:")
b = input()
#c = "PiSense alert"
c = config['notification']['pisense_alert_notification']
notification(eventName, key, a, b, c)
