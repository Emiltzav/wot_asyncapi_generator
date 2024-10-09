from . import asyncapioperations

from Database.DatabaseEmulator import DatabaseSim
from ...Tools.Asyncapi.ObjectsExtractor import AsyncapiExtractor

from flask import jsonify, request


@asyncapioperations.route('/', methods=['GET'])
def test():
    return jsonify({"msg":'this a test'})


# ================================= Web Thing Resource Channel Functionality ================================= 

"""
     Create Web Thing Resource Operation. 
     Attention: It will create a NEW Operation so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "action":"send" | "receive" (Required),
                "channel":TYPE(object) (Required),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "security":TYPE(object)
                "traits":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object),
                "messages":TYPE(object),
                "reply":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapioperations.route('/createWTRO/<docID>', methods=['PUT'])
def createWTRO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "webthing_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        
        #check if input empty
        if not inputData:
            objExtractor.operations[OPERATION_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            if 'action' not in inputData:
                return jsonify({"msg":"error"})

            if 'channel' not in inputData:
                return jsonify({"msg":"error"})
            objExtractor.operations[OPERATION_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Web Thing Resource Operation. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"action":""})

"""
@asyncapioperations.route('/updateWTRO/<docID>', methods=['POST'])
def updateWTRO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "webthing_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.operations[OPERATION_NAME]:
                    del objExtractor.operations[OPERATION_NAME][key]
            else:
                objExtractor.operations[OPERATION_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapioperations.route('/deleteWTRO/<docID>', methods=['DELETE'])
def deleteWTRO(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "webthing_resource_operation"
        
        
        if OPERATION_NAME in objExtractor.operations:
            del objExtractor.operations[OPERATION_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Web Thing Resource Operation Functionality ================================= 

# ================================= Properties Channel Functionality ================================= 

"""
     Create Properties Resource Operation. 
     Attention: It will create a NEW Operation so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "action":"send" | "receive" (Required),
                "channel":TYPE(object) (Required),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "security":TYPE(object)
                "traits":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object),
                "messages":TYPE(object),
                "reply":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapioperations.route('/createPRO/<docID>', methods=['PUT'])
def createPRO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "properties_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        
        #check if input empty
        if not inputData:
            objExtractor.operations[OPERATION_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            if 'action' not in inputData:
                return jsonify({"msg":"error"})

            if 'channel' not in inputData:
                return jsonify({"msg":"error"})
            objExtractor.operations[OPERATION_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Properties Resource Operation. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"action":""})

"""
@asyncapioperations.route('/updatePRO/<docID>', methods=['POST'])
def updatePRO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "properties_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.operations[OPERATION_NAME]:
                    del objExtractor.operations[OPERATION_NAME][key]
            else:
                objExtractor.operations[OPERATION_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapioperations.route('/deletePRO/<docID>', methods=['DELETE'])
def deletePRO(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "properties_resource_operation"
        
        
        if OPERATION_NAME in objExtractor.operations:
            del objExtractor.operations[OPERATION_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Properties Resource Operation Functionality ================================= 

# ================================= Actions Channel Functionality ================================= 

"""
     Create Actions Resource Operation. 
     Attention: It will create a NEW Operation so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "action":"send" | "receive" (Required),
                "channel":TYPE(object) (Required),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "security":TYPE(object)
                "traits":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object),
                "messages":TYPE(object),
                "reply":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapioperations.route('/createARO/<docID>', methods=['PUT'])
def createARO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "actions_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        
        #check if input empty
        if not inputData:
            objExtractor.operations[OPERATION_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            if 'action' not in inputData:
                return jsonify({"msg":"error"})

            if 'channel' not in inputData:
                return jsonify({"msg":"error"})
            objExtractor.operations[OPERATION_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Actions Resource Operation. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"action":""})

"""
@asyncapioperations.route('/updateARO/<docID>', methods=['POST'])
def updateARO(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "actions_resource_operation"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.operations[OPERATION_NAME]:
                    del objExtractor.operations[OPERATION_NAME][key]
            else:
                objExtractor.operations[OPERATION_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapioperations.route('/deleteARO/<docID>', methods=['DELETE'])
def deleteARO(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        OPERATION_NAME = "actions_resource_operation"
        
        
        if OPERATION_NAME in objExtractor.operations:
            del objExtractor.operations[OPERATION_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Actions Resource Operation Functionality ================================= 
"""
    Load Document from database.
    Return:
        Database ref for further use.
        Asyncapi objects
"""
def Wizard(docID):
    dbe = DatabaseSim()
    theDoc = dbe.LoadData(docID)
    # Use the tools to extract each object from the asyncapi document
    objExtractor = AsyncapiExtractor()
    objExtractor.asyncDoc = theDoc
    objExtractor.ExtractAll()

    return dbe, objExtractor