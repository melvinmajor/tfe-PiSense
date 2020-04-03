from flask import Flask
from .views import app
from .models import db, User, Box, users_schema, user_schema, box_schema
#from . import models

app.static_folder = 'static'

# Connect sqlalchemy to app
models.db.init_app(app)


@app.cli.command()
def init_db():
    models.init_db()
