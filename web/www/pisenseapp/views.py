from flask import Flask, render_template

app = Flask(__name__)

# Config options - Make sure you created a 'config.py' file.
app.config.from_object('config')
# To get one variable, tape app.config['MY_VARIABLE']

@app.route('/')
@app.route('/index.html')
def index():
    return render_template('index.html',
                           page="Accueil")

@app.route('/about.html')
def about():
    return render_template('about.html',
                           page="A propos")

@app.route('/order.html')
def order():
    return render_template('order.html',
                           page="Commander")

@app.route('/register.html')
def register():
    return render_template('register.html',
                           page="Connexion/Enregistrement")

@app.route('/404.html')
def error():
    return render_template('404.html',
                           page="Erreur 404")

@app.route('/50x.html')
def errorBis():
    return render_template('50x.html',
                           page="Erreur 50x")

if __name__ == "__main__":
    app.run()
