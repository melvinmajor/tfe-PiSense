import os
from flask_sqlalchemy import SQLAlchemy

basedir = os.path.abspath(os.path.dirname(__file__))
SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://root:9RjpFU8BHvnV@localhost:3306/MariaDB'

# Test if it works
engine = SQLAlchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
print(engine.table_names())
