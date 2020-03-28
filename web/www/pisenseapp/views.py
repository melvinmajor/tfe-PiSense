from flask import Flask, render_template

app = Flask(__name__)

@app.route('/')
@app.route('/index.html')
def index():
    return render_template('index.html',
                           site_name="PiSense",
                           page="Accueil")

@app.route('/about.html')
def about():
    return render_template('about.html',
                           site_name="PiSense",
                           page="A propos")

@app.route('/order.html')
def order():
    return render_template('order.html',
                           site_name="PiSense",
                           page="Commander")

@app.route('/register.html')
def register():
    return render_template('register.html',
                           site_name="PiSense",
                           page="Connexion/Enregistrement")

@app.route('/404.html')
def error():
    return render_template('404.html',
                           site_name="PiSense",
                           page="Erreur 404")

@app.route('/50x.html')
def errorBis():
    return render_template('50x.html',
                           site_name="PiSense",
                           page="Erreur 50x")

if __name__ == "__main__":
    app.run()
