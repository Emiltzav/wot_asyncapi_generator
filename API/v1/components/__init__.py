from flask import Blueprint

asyncapicomponents = Blueprint('asyncapi_components', __name__)

from . import routes