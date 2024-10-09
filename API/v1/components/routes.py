from . import asyncapicomponents
import json
import os

from flask import jsonify, request



@asyncapicomponents.route('/', methods=['PUT'])
def test():
    return jsonify({"msg":"This is a test"})


