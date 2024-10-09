from flask import Blueprint

asyncapioperations = Blueprint('asyncapi_operations', __name__)

from . import routes