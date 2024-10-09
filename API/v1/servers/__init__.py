from flask import Blueprint

asyncapiservers = Blueprint('asyncapi_servers', __name__)

from . import routes