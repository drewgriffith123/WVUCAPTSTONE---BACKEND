from flask import Flask
from flask_cors import CORS, cross_origin

app = Flask(__name__)
CORS(app)

@app.route("/")
def index():
    return "<h1>Hello!</h1>"

if __name__ == "__main__":
    from waitress import serve
    serve(app, host="0.0.0.0", port=8080)

