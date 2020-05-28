from flask_sqlalchemy import SQLAlchemy
from flask_marshmallow import Marshmallow
from sqlalchemy.dialects.mysql import VARCHAR
from .views import app
import enum
import datetime
import logging as lg

# Create database connection object
db = SQLAlchemy(app)

# Init Marshmallow
ma = Marshmallow(app)


""" Enum of all sensors available
"""


class SensorsEnum(enum.Enum):
    none = 0
    bmp280 = 1
    bme280 = 2
    bme680 = 3
    sds011 = 4


""" Box Class/Model
"""


class Box(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    datetime = db.Column(db.DateTime(timezone=True), nullable=False)
    temperature = db.Column(db.Float(4,1))
    humidity = db.Column(db.Float(4,1))
    pressure = db.Column(db.Float(6,2))
    gas = db.Column(db.Float(6,2))
    PM2 = db.Column(db.Float(6,4))
    PM10 = db.Column(db.Float(6,4))

    def __init__(self, id, datetime, temperature, humidity, pressure, gas, pm2, pm10):
        self.id = id
        self.datetime = datetime
        self.temperature = temperature
        self.humidity = humidity
        self.pressure = pressure
        self.gas = gas
        self.pm2 = pm2
        self.pm10 = pm10


""" Box Schema
"""


class BoxSchema(ma.Schema):
    class Meta:
        fields = ('id', 'datetime', 'temperature', 'humidity', 'pressure', 'gas', 'pm2', 'pm10')


""" User Class/Model
"""


class User(db.Model):
    userID = db.Column(db.Integer, primary_key=True)
    mail = db.Column(db.String(length=50), nullable=False)
    password = db.Column(db.String(length=32), nullable=False)
    name = db.Column(db.String(length=100), nullable=False)
    firstname = db.Column(db.String(length=100), nullable=False)
    phone = db.Column(db.String(length=12), nullable=False)
    dateRegistered = db.Column(db.DateTime(timezone=True), nullable=False)
    device = db.Column(db.Boolean, default=0)
    deviceOutdoor = db.Column(db.Boolean, default=0)
    device_id = db.Column(db.Integer, db.ForeignKey('box.id'))
    sensors = db.Column(db.Enum(SensorsEnum), default=0)

    def __init__(self, mail, password, name, firstname, phone, dateRegistered, device, deviceOutdoor, device_id,
                 sensors):
        self.mail = mail
        self.password = password
        self.name = name
        self.firstname = firstname
        self.phone = phone
        self.dateRegistered = dateRegistered
        self.device = device
        self.deviceOutdoor = deviceOutdoor
        self.device_id = device_id
        self.sensors = sensors


""" User Schema
"""


class UserSchema(ma.Schema):
    class Meta:
        fields = ('name', 'firstname', 'device', 'deviceOutdoor', 'device_id', 'sensors')


""" Init Schema
"""
user_schema = UserSchema()
users_schema = UserSchema(many=True)
box_schema = BoxSchema()
boxes_schema = BoxSchema(many=True)


db.create_all()


""" Init DB
"""


def init_db():
    # from pisenseapp.models import db, SensorsEnum, User, Box, datetime
    db.drop_all()
    db.create_all()
    db.session.add(User('toto@hotmail.com', 'P@ssw0rd', 'Babar', 'Tortue', '+32475123456', datetime.datetime.now(),
                        false, false, 0, SensorsEnum['none']))
    db.session.commit()
    lg.warning('Database initialized!')
