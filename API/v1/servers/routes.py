from . import asyncapiservers
from Database.DatabaseEmulator import DatabaseSim
from ...Tools.Asyncapi.ObjectsExtractor import AsyncapiExtractor

from flask import jsonify, request



@asyncapiservers.route('/', methods=['PUT'])
def test():
    return jsonify({"msg":"This is a test"})


""" 
    Create a new server.

    A json must be send with the following fiels:
        documentID : Required
        serverID : Required
        host : Required
        protocol : Required
        pathname : Not Requred
        title : Not Requred
        summary : Not Requred
        discription : Not Requred
        protocolVersion : Not Requred
        variables : Not Requred
        security : Not Requred
        tags : Not Requred
        externalDocs : Not Requred
        bindings : Not Requred

"""
@asyncapiservers.route('/create', methods=['PUT'])
def createServer():
    serverObj = request.get_json()
    try:
        if serverObjectValidation(serverObj):
            # Extract the document ID and load it from database
            documentID = serverObj["documentID"]
            serverID = str(serverObj['serverID'])
            dbe = DatabaseSim()
            theDoc = dbe.LoadData(documentID)
            
            # Use the tools to extract each object from the asyncapi document
            objExtractor = AsyncapiExtractor()
            objExtractor.asyncDoc = theDoc
            
            objExtractor.ExtractAll()
            
            """
                Now that we have All the objects we will append the new server object in components object
                and we will create a ref in the servers object.
                Steps:
                    1) Given the request data we will extract the data tha match the asyncapi server object.
                    2) Append the new server object in components object.
                    3) Create a ref in the servers object.

            """ 
            newServerObj = serverObjectGenerator(serverObj)
            objExtractor.servers[str(serverID)] = newServerObj
            newAsyncapiDoc = objExtractor.PackAll()
            dbe.StoreData(documentID, newAsyncapiDoc)

            return jsonify({"msg":"success"})
        else:
            return jsonify({"msg":"fail"})
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"})

@asyncapiservers.route('/read/<serverID>/<docID>', methods=['GET'])
def getServerInfo(docID, serverID):
    dbe = DatabaseSim()
    theDoc = dbe.LoadData(docID)

    # Use the tools to extract each object from the asyncapi document
    objExtractor = AsyncapiExtractor()
    objExtractor.asyncDoc = theDoc
    objExtractor.ExtractAll()

    if serverID not in objExtractor.servers:
        if serverID not in objExtractor.components['servers']:
            return jsonify({"msg":"fail"})
        

    if "$ref" in objExtractor.servers[serverID]:
        return objExtractor.components['servers'][serverID]
    else:
        return objExtractor.servers[serverID]

@asyncapiservers.route('/delete/<serverID>/<docID>', methods=['DELETE'])
def deleteServer(docID, serverID): 
    try:
        # Load document and extract objects 
        dbe = DatabaseSim()
        theDoc = dbe.LoadData(docID)
        
        objExtractor = AsyncapiExtractor()
        objExtractor.asyncDoc = theDoc
        objExtractor.ExtractAll()

        if serverID not in objExtractor.servers:
            if serverID not in objExtractor.components['servers']:
                return jsonify({"msg":"fail"})
        
         
        
        if serverID in objExtractor.components['servers']:
            del objExtractor.components['servers'][serverID]
        if serverID  in objExtractor.servers:
            del objExtractor.servers[serverID]
        newAsyncapiDoc = objExtractor.PackAll()
        dbe.StoreData(docID, newAsyncapiDoc)

        return jsonify({"msg":"success"})
        
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"})



"""
    Upadate the fields of server object.
    Input:
        Json with fields to be changed.
            Example: {"pathname":"\production"}
        Delete a field leaving the value empty.
            Example: {"pathname":""}
"""
@asyncapiservers.route('/update/<serverID>/<docID>', methods=['POST'])
def updateServer(docID, serverID):
    newServerData = request.get_json()
    
    try:
        # Load document and extract asyncapi objects
        dbe = DatabaseSim()
        theDoc = dbe.LoadData(docID)
        
        objExtractor = AsyncapiExtractor()
        objExtractor.asyncDoc = theDoc
        objExtractor.ExtractAll()

        # Validation
        if serverID not in objExtractor.servers:
            if serverID not in objExtractor.components['servers']:
                return jsonify({"msg":"fail"})
        
        
        # Search if the server data are inside servers object or components object
        if "$ref" in objExtractor.servers[serverID]:
            
            for key, value in newServerData:
                if key in objExtractor.components[serverID] and value == "":
                    del objExtractor.components[serverID][key]
                else:
                    objExtractor.components[serverID][key] = value
        else:
            
            for key, value in newServerData.items():
                if key in objExtractor.servers[serverID] and value == "":
                    del objExtractor.servers[serverID][key] 
                else:
                    objExtractor.servers[serverID][key] = value

        
        newAsyncapiDoc = objExtractor.PackAll()
        dbe.StoreData(docID, newAsyncapiDoc)

        return jsonify({"msg":"success"})
        
    except Exception as e:
        print(e)
        return jsonify({"msg":"fail"})

"""
    Given a server object this method will check if the required fields exist.
    Note: Not fully functional yet
    TODO: must check all fields and their types
"""
def serverObjectValidation(serverObj):
    fixed_fields = ['host', 'protocol', 'documentID', 'serverID']
    for item in fixed_fields:
        if item not in serverObj:
            return False
    return True


"""
    Extract the data from the request json and
    create an asycapi server objsect
"""
def serverObjectGenerator(requestData):
    # We use a copy so we wont destroy the original data
    requestDataCopy = requestData

    # remove the unwanted data
    del requestDataCopy['documentID']
    del requestDataCopy['serverID']

    # return the asyncapi-ready servers object
    return requestDataCopy