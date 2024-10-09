from flask import Blueprint

asyncapidoc = Blueprint('asyncapi_doc', __name__)

from . import routes