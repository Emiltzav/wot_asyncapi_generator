from . import asyncapichannels

from Database.DatabaseEmulator import DatabaseSim
from ...Tools.Asyncapi.ObjectsExtractor import AsyncapiExtractor

from flask import jsonify, request


@asyncapichannels.route('/', methods=['GET'])
def test():
    return jsonify({"msg":'this a test'})


# ================================= Web Thing Resource Channel Functionality ================================= 

"""
     Create Web Thing Resource Channel. 
     Attention: It will create a NEW channel so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "address":TYPE(string),
                "messages":TYPE(object),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "servers":TYPE(object)
                "parameters":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapichannels.route('/createWTRC/<docID>', methods=['PUT'])
def createWTRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "webthing_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        #check if input empty
        if not inputData:
            objExtractor.channels[CHANNEL_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            objExtractor.channels[CHANNEL_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Web Thing Resource Channel. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"address":""})

"""
@asyncapichannels.route('/updateWTRC/<docID>', methods=['POST'])
def updateWTRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "webthing_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.channels[CHANNEL_NAME]:
                    del objExtractor.channels[CHANNEL_NAME][key]
            else:
                objExtractor.channels[CHANNEL_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapichannels.route('/deleteWTRC/<docID>', methods=['DELETE'])
def deleteWTRC(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "webthing_resource_channel"
        
        
        if CHANNEL_NAME in objExtractor.channels:
            del objExtractor.channels[CHANNEL_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Web Thing Resource Channel Functionality ================================= 


# ================================= Properties Resource Channel Functionality ================================= 

"""
     Create Properties Resource Channel. 
     Attention: It will create a NEW channel so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "address":TYPE(string),
                "messages":TYPE(object),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "servers":TYPE(object)
                "parameters":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapichannels.route('/createPRC/<docID>', methods=['PUT'])
def createPropertiesRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "properties_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        #check if input empty
        if not inputData:
            objExtractor.channels[CHANNEL_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            objExtractor.channels[CHANNEL_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Properties Resource Channel. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"address":""})

"""
@asyncapichannels.route('/updatePRC/<docID>', methods=['POST'])
def updatePropertiesRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "properties_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.channels[CHANNEL_NAME]:
                    del objExtractor.channels[CHANNEL_NAME][key]
            else:
                objExtractor.channels[CHANNEL_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapichannels.route('/deletePRC/<docID>', methods=['DELETE'])
def deletePropertiesRC(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "properties_resource_channel"
        
        
        if CHANNEL_NAME in objExtractor.channels:
            del objExtractor.channels[CHANNEL_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Properties Resource Channel Functionality ================================= 


# ================================= Actions Resource Channel Functionality ================================= 

"""
     Create Actions Resource Channel. 
     Attention: It will create a NEW channel so it will DELETE any previous entry.
     Input (optional):
        A Json that contain the info needed
            {
                "address":TYPE(string),
                "messages":TYPE(object),
                "title":TYPE(string)
                "summary":TYPE(string)
                "description":TYPE(string)
                "servers":TYPE(object)
                "parameters":TYPE(object)
                "tags":TYPE(list of objects)
                "externalDocs":TYPE(object)
                "bindings":TYPE(object)
            }
        Input can be empty BUT NOT absent.
"""
@asyncapichannels.route('/createARC/<docID>', methods=['PUT'])
def createActionsRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "actions_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        #check if input empty
        if not inputData:
            objExtractor.channels[CHANNEL_NAME] = {}
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
        else:
            objExtractor.channels[CHANNEL_NAME] = inputData
            newDoc = objExtractor.PackAll()
            dbe.StoreData(docID, newDoc)
            return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
"""
    Update the data of Properties Resource Channel. 
    Input is a json with the fields to be updated. 
    if a field value is equal to "" the field will be deleted. (Example: {"address":""})

"""
@asyncapichannels.route('/updateARC/<docID>', methods=['POST'])
def updateActionsRC(docID):
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "actions_resource_channel"
        
        # check for valid json
        if not request.is_json:
            return jsonify({"msg":"fail"})
        

        inputData = request.get_json()
        for key, value in inputData.items():
            if value == "":
                if key in objExtractor.channels[CHANNEL_NAME]:
                    del objExtractor.channels[CHANNEL_NAME][key]
            else:
                objExtractor.channels[CHANNEL_NAME][key] = value
        
        # Save to database
        updatedDoc = objExtractor.PackAll()
        dbe.StoreData(docID, updatedDoc)
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
    
@asyncapichannels.route('/deleteARC/<docID>', methods=['DELETE'])
def deleteActionsRC(docID): 
    try:
        dbe, objExtractor = Wizard(docID)
        CHANNEL_NAME = "actions_resource_channel"
        
        
        if CHANNEL_NAME in objExtractor.channels:
            del objExtractor.channels[CHANNEL_NAME]
            # Save to database
            updatedDoc = objExtractor.PackAll()
            dbe.StoreData(docID, updatedDoc)
        
        
        return jsonify({"msg":"success"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"}) 
# ================================= Actions Resource Channel Functionality ================================= 



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