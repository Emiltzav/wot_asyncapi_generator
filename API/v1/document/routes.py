from . import asyncapidoc 
import json
import os

from flask import jsonify, request
from Database.DatabaseEmulator import DatabaseSim
from ...Tools.Asyncapi.ObjectsExtractor import AsyncapiExtractor

@asyncapidoc.route('/createempty/<docID>', methods=['PUT'])
def createFromEmptyDoc(docID):
    
    try:
        docID = str(docID)
        file_path = os.path.join(os.path.dirname(__file__), 'templates', 'emptyAsyncapiTemplate.json')
        
        with open(file_path, 'r') as f:
            theDoc = json.load(f)
        
        dbe = DatabaseSim()
        dbe.StoreData(docID, theDoc)

        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"})
    

@asyncapidoc.route('/create/<docID>', methods=['PUT'])
def createDoc(docID):
    """
        To DO:
            Need to check if the user input is a valid Thing Description Template
    """
    try:
        newAsyncApiDoc = request.get_json()
         
        docID = str(docID)
        objExtractor = AsyncapiExtractor()
        objExtractor.asyncDoc = newAsyncApiDoc
        objExtractor.ExtractAll()
        newAsyncApiDoc = objExtractor.PackAll()
        dbe = DatabaseSim()
        dbe.StoreData(docID, newAsyncApiDoc)

        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({'msg': 'fail'})
    
@asyncapidoc.route('/delete/<docID>', methods=['DELETE'])
def deleteDoc(docID):

    try:
        
        dbe = DatabaseSim()
        dbe.DeleteDocument(docID)

        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({'msg': 'fail'})