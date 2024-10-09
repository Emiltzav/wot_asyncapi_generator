from flask import Flask
from flask import jsonify
from flask_cors import CORS

from .document import asyncapidoc
from .components import asyncapicomponents
from .servers import asyncapiservers
from .channels import asyncapichannels
from .operations import asyncapioperations
from .messages import asyncapimessages


def create_app(test_config=None):
    # create and configure the app
    app = Flask(__name__)
    CORS(app)
    app.register_blueprint(asyncapidoc, url_prefix='/doc')
    app.register_blueprint(asyncapicomponents, url_prefix='/components')
    app.register_blueprint(asyncapiservers, url_prefix='/servers')
    app.register_blueprint(asyncapichannels, url_prefix='/channels')
    app.register_blueprint(asyncapioperations, url_prefix='/operations')
    app.register_blueprint(asyncapimessages, url_prefix='/messages')

    # return the api version
    @app.route('/')
    def apiVersion():
        return jsonify({'version':'1.0'})

    return app
    
