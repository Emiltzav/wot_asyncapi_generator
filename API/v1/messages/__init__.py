from flask import Blueprint

asyncapimessages = Blueprint('asyncapi_messages', __name__)

from . import routes