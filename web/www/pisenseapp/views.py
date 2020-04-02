from flask import Flask, render_template, request, jsonify
from .models import db

app = Flask(__name__)

# Config options - Make sure you created a 'config.py' file.
app.config.from_object('config')


# To get one variable, tape app.config['MY_VARIABLE']

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


@app.route('/404.html', methods=['GET'])
def error():
    return render_template('404.html',
                           page="Erreur 404")


@app.route('/50x.html', methods=['GET'])
def errorbis():
    return render_template('50x.html',
                           page="Erreur 50x")


@app.route('new-user', methods=['POST'])
def add_user():
    mail = request.jsonify['mail']
    password = request.jsonify['password']
    firstname = request.jsonify['firstname']
    name = request.jsonify['name']
    phone = request.jsonify['phone']

    new_user = User(mail, password, firstname, name, phone)
    db.session.add(new_user)
    db.session.commit()

    return user_schema.jsonify(new_user)

if __name__ == "__main__":
    app.run()
