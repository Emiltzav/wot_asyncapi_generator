from flask import Blueprint

asyncapichannels = Blueprint('asyncapi_channels', __name__)

from . import routes