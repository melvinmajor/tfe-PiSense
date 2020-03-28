from flask import Flask
from .views import app

app.static_folder = 'static'
