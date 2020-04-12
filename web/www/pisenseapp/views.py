from flask import Flask, render_template, request, jsonify
import datetime
import pisenseapp.models

app = Flask(__name__)

# Config options - Make sure you created a 'config.py' file.
app.config.from_object('config')
# To get one variable, tap app.config['MY_VARIABLE']


@app.route('/', methods=['GET'])
@app.route('/index.html', methods=['GET'])
def index():
    return render_template('index.html',
                           page="Accueil")


@app.route('/about.html', methods=['GET'])
def about():
    return render_template('about.html',
                           page="A propos")


@app.route('/order.html', methods=['GET'])
def order():
    return render_template('order.html',
                           page="Commander")


@app.route('/register.html', methods=['GET'])
def register():
    return render_template('register.html',
                           page="Connexion/Enregistrement")


# @app.route('/404.html', methods=['GET'])
# def error():
#     return render_template('404.html',
#                            page="Erreur 404")
#
#
# @app.route('/50x.html', methods=['GET'])
# def errorbis():
#     return render_template('50x.html',
#                            page="Erreur 50x")

@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html',
                           page="Erreur 404"), 404


@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html',
                           page="Erreur 404"), 404


@app.errorhandler(500)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('50x.html',
                           page="Erreur 500"), 500


@app.errorhandler(502)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('50x.html',
                           page="Erreur 502"), 502


@app.errorhandler(503)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('50x.html',
                           page="Erreur 503"), 503


@app.errorhandler(504)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('50x.html',
                           page="Erreur 504"), 504


# API: Create a new user
@app.route('/user', methods=['POST'])
def add_user():
    id = request.json['id']
    mail = request.json['mail']
    password = request.json['password']
    name = request.json['name']
    firstname = request.json['firstname']
    phone = request.json['phone']
    date_registered = datetime.datetime.now()
    device = request.json['device']
    device_outdoor = request.json['device_outdoor']
    device_id = request.json['device_id']
    sensors = request.json['sensors']

    new_user = pisenseapp.models.User(id, mail, password, name, firstname, phone, date_registered, device, device_outdoor, device_id, sensors)

    pisenseapp.models.db.session.add(new_user)
    pisenseapp.models.db.session.commit()

    return pisenseapp.models.user_schema.jsonify(new_user)


# API: See all users
@app.route('/user', methods=['GET'])
def get_users():
    all_users = pisenseapp.models.User.query.all()
    result = pisenseapp.models.users_schema.dump(all_users)
    return jsonify(result.data)


# API: See specific user based on id
@app.route('/user/<id>', methods=['GET'])
def get_user(id):
    user = pisenseapp.models.User.query.get(id)
    result = pisenseapp.models.users_schema.dump(user)
    return jsonify(result.data)


# API: Add new values from environmental sensors
@app.route('/box', methods=['POST'])
def add_box_info():
    id = request.json['id']
    datetime = request.json['datetime']
    temperature = request.json['temperature']
    humidity = request.json['humidity']
    pressure = request.json['pressure']
    gas = request.json['gas']
    pm2 = request.json['pm2']
    pm10 = request.json['pm10']

    new_box_info = pisenseapp.models.Box(id, datetime, temperature, humidity, pressure, gas, pm2, pm10)

    pisenseapp.models.db.session.add(new_box_info)
    pisenseapp.models.db.session.commit()

    return pisenseapp.models.box_schema.jsonify(new_box_info)


# API: see values from environmental sensors stored in database from a specific box based on id
@app.route('/box/<id>', methods=['GET'])
def get_box(id):
    box = pisenseapp.models.Box.query.get(id)
    result = pisenseapp.models.box_schema.dump(box)
    return jsonify(result.data)


if __name__ == "__main__":
    app.run()
