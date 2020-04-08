from flask import Flask

from . import models
from .views import app
# from .models import db, User, Box, users_schema, user_schema, box_schema

app.static_folder = 'static'

# Connect sqlalchemy to app
models.db.init_app(app)


# FLASK_APP=run.py flask init_db
@app.cli.command()
def init_db():
    models.init_db()
