from flask import Flask, render_template, request, jsonify, url_for
import datetime
import pisenseapp.models
from werkzeug.utils import redirect

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


@app.route('/platform.html', methods=['GET'])
def platform():
    return render_template('platform.html',
                           page="Plateforme utilisateur",
                           user_firstname="Melvin", user_name="Campos Casares", user_sensors="BME680, SDS011", user_boxid="0")


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


@app.errorhandler(403)
def page_not_found(e):
    return render_template('error/403.html',
                           page="Erreur 403"), 403


@app.errorhandler(404)
def page_not_found(e):
    return render_template('error/404.html',
                           page="Erreur 404"), 404


@app.errorhandler(405)
def page_not_found(e):
    return render_template('error/405.html',
                           page="Erreur 405"), 405


@app.errorhandler(500)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('error/50x.html',
                           page="Erreur 500"), 500


@app.errorhandler(502)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('error/50x.html',
                           page="Erreur 502"), 502


@app.errorhandler(503)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('error/50x.html',
                           page="Erreur 503"), 503


@app.errorhandler(504)
def internal_error(exception):
    app.logger.exception(exception)
    return render_template('error/50x.html',
                           page="Erreur 504"), 504


""" API:
    User connection
"""


@app.route('/login', methods=["GET", "POST"])
def login_page():
    error = ''
    server_error = "Une erreur est survenue, veuillez entrer à nouveau vos informations de connexion."
    try:

        if request.method == "POST":

            attempted_mail = request.form['mail']
            attempted_password = request.form['password']

            # Test to check if the comparison is working, need to link that with s74.cwb.ovh user database
            if attempted_mail == "toto@hotmail.com" and attempted_password == "P@ssw0rd!":
                return redirect(url_for('platform'))
            elif attempted_mail == "" and attempted_password == "":
                error = server_error
            else:
                error = "Les informations d'identification sont invalides, veuillez réessayer."

        # return render_template("platform.html", error=error)
        return render_template("register.html", page="Connexion/Enregistrement", error=error)

    except Exception as e:
        return render_template("register.html", page="Connexion/Enregistrement", error=server_error)


""" API:
    Create a new user
"""


@app.route('/register', methods=['POST'])
def add_user():
    mail = request.form['mail']
    password = request.form['password']
    name = request.form['name']
    firstname = request.form['firstname']
    phone = request.form['phone']
    date_registered = datetime.datetime.now()
    device = request.form['device']
    if device == "true":
        device = True
    elif device == "false":
        device = False
    device_outdoor = request.form['device_outdoor']
    if device_outdoor == "true":
        device_outdoor = True
    elif device_outdoor == "false":
        device_outdoor = False
    device_id = request.form['device_id']
    sensors = request.form['sensors']
    sensors = pisenseapp.models.SensorsEnum[sensors]

    existing_user = pisenseapp.models.User.query.filter(
        (pisenseapp.models.User.mail == mail) |
        ((pisenseapp.models.User.firstname == firstname) & (pisenseapp.models.User.name == name))
    ).first()

    # Check if user already exist in database
    if existing_user:
        response = jsonify({"message": "User already exists"})
        response.status_code = 409
        return response

    new_user = pisenseapp.models.User(mail, password, name, firstname, phone, date_registered, device, device_outdoor,
                                      device_id, sensors)

    pisenseapp.models.db.session.add(new_user)
    pisenseapp.models.db.session.commit()

    new_user.sensors = str(new_user.sensors)

    # Status OK, object created
    response = jsonify({"message": "User created"})
    response.status_code = 201
    return response


""" API:
    See all users
"""


@app.route('/user', methods=['GET'])
def get_users():
    all_users = pisenseapp.models.User.query.all()
    result = pisenseapp.models.users_schema.dump(all_users)
    return jsonify(result.data)


""" API:
    See specific user based on id
"""


@app.route('/user/<id>', methods=['GET'])
def get_user(userID):
    user = pisenseapp.models.User.query.get(userID)
    result = pisenseapp.models.users_schema.dump(user)
    return jsonify(result.data)


""" API:
    Add new values from environmental sensors
"""


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

    # Status OK, object created
    response = jsonify({"message": "Created"})
    response.status_code = 201
    return response


""" API:
    see values from environmental sensors stored in database from a specific box based on id
"""


@app.route('/box/<id>', methods=['GET'])
def get_box(id):
    box = pisenseapp.models.Box.query.get(id)
    result = pisenseapp.models.box_schema.dump(box)
    return jsonify(result.data)


if __name__ == "__main__":
    app.run()
