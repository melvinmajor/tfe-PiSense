from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.dialects.mysql import VARCHAR
from .views import app
import enum
import datetime
import logging as lg

# Create database connection object
db = SQLAlchemy(app)


class SensorsEnum(enum.Enum):
    none = 0
    bmp280 = 1
    bme280 = 2
    bme680 = 3
    sds011 = 4


class User(db.Model):
    userID = db.Column(db.Integer, primary_key=True)
    mail = db.Column(VARCHAR(length=50), nullable=False)
    password = db.Column(VARCHAR(length=32), nullable=False)
    name = db.Column(VARCHAR(length=100), nullable=False)
    firstname = db.Column(VARCHAR(length=100), nullable=False)
    phone = db.Column(VARCHAR(length=12), nullable=False)
    dateRegistered = db.Column(db.DateTime(timezone=True), nullable=False)
    device = db.Column(db.Boolean, default=0)
    deviceOutdoor = db.Column(db.Boolean, default=0)
    sensors = db.Column(db.Enum(SensorsEnum), default=0)

    def __init__(self, mail, password, name, firstname, phone, dateRegistered, device, deviceOutdoor, sensors):
        self.mail = mail
        self.password = password
        self.name = name
        self.firstname = firstname
        self.phone = phone
        self.dateRegistered = dateRegistered
        self.device = device
        self.deviceOutdoor = deviceOutdoor
        self.sensors = sensors


class Box(db.Model):
    boxID = db.Column(db.Integer, primary_key=True)
    datetime = db.Column(db.DateTime(timezone=True), nullable=False)
    temperature = db.Column(db.Float(4,1))
    humidity = db.Column(db.Float(4,1))
    pressure = db.Column(db.Float(6,2))
    gas = db.Column(db.Float(6,2))
    PM2 = db.Column(db.Float(6,4))
    PM10 = db.Column(db.Float(6,4))

    def __init__(self, datetime, temperature, humidity, pressure, gas, pm2, pm10):
        self.datetime = datetime
        self.temperature = temperature
        self.humidity = humidity
        self.pressure = pressure
        self.gas = gas
        self.pm2 = pm2
        self.pm10 = pm10

db.create_all()


def init_db():
    # from pisenseapp.models import db, SensorsEnum, User, Box, datetime
    db.drop_all()
    db.create_all()
    db.session.add(User('toto@hotmail.com', 'P@ssw0rd', 'Babar', 'Tortue', '+32475123456', datetime.datetime.now(), 0, 0, 'none'))
    db.session.commit()
    lg.warning('Database initialized!')
