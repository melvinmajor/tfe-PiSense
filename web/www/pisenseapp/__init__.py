from flask import Flask
from .views import app
from . import models

app.static_folder = 'static'

models.db.init_app(app)
