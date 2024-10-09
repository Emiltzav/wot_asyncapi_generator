from . import asyncapimessages

from Database.DatabaseEmulator import DatabaseSim
from ...Tools.Asyncapi.ObjectsExtractor import AsyncapiExtractor
from ...Tools.Utils.apiResponses import response_message, response_codes, API_FAIL, API_SUCCESS

from flask import jsonify, request


@asyncapimessages.route('/', methods=['GET'])
def test():
    return jsonify({"msg":'this a test'})

"""
    Input:
        Message object -> defined in asyncapi documentation.
        MessageId -> Unique string that identifies the message.
        docID -> Unique string that identifies the asyncapi json the message will be stored in.
    
    How it works:
        1) Load the document from database
        2) Extract each object from that document (channels, operation etc)
        3) Append into messages object the new message.
        4) Store everything back to the database.
"""
@asyncapimessages.route('/create/<messageID>/<channelID>/<docID>', methods =['PUT'])
def createMessage(messageID, channelID, docID):
    try:
        # Load Doc
        dbe, objExtractor = Wizard(docID)
        if channelID in objExtractor.channels:
            if channelID in objExtractor.messages: 
                objExtractor.messages[channelID][messageID] = request.get_json()
                theData = objExtractor.PackAll()
                dbe.StoreData(docID, theData)
            else:
                objExtractor.messages[channelID] = {}
                objExtractor.messages[channelID][messageID] = request.get_json()
                theData = objExtractor.PackAll()
                dbe.StoreData(docID, theData)

            # ================= Response Construction =========================
            response_message.description = "New message object created!"
            response_message.msg = API_SUCCESS
            response_message.code = response_codes.success_code
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        else:
            # ================= Response Construction =========================
            response_message.description = "Failed to create new message becauses channel does not exist!"
            response_message.msg = API_FAIL
            response_message.code = response_codes.wrong_channel
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        
    except Exception as e:
        print(e)
        # ================= Response Construction =========================
        response_message.description = "Failed to create new message becauses of some internal error!"
        response_message.msg = API_FAIL
        response_message.code = response_codes.internal_error
        # ================= Response Construction =========================
        return jsonify(response_message.msg2dict())


"""
    Update a specified message.
    Input:
        Message object -> defined in asyncapi documentation. (To delete a field set fields value equal to "", example: name:"")
        MessageId -> Unique string that identifies the message.
        docID -> Unique string that identifies the asyncapi json the message will be stored in.
"""
@asyncapimessages.route('/update/<messageID>/<channelID>/<docID>', methods=['POST'])
def updateMessage(messageID, channelID, docID):
    try:
        # Load Doc
        dbe, objExtractor = Wizard(docID)
        if messageID not in objExtractor.messages:
            # ================= Response Construction =========================
            response_message.description = "The message you are trying to update does not exist!"
            response_message.msg = API_FAIL
            response_message.code = response_codes.fail_code
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        if channelID in objExtractor.channels:
            if channelID in objExtractor.messages: 
                inputData = request.get_json()
                for key, val in inputData.items():
                    if val == "":
                        if key in objExtractor.messages[channelID][messageID]:
                            del objExtractor.messages[channelID][messageID][key]
                    else:
                        objExtractor.messages[channelID][messageID][key] = inputData[key]

                
                theData = objExtractor.PackAll()
                dbe.StoreData(docID, theData)
            else:
                objExtractor.messages[channelID] = {}
                objExtractor.messages[channelID][messageID] = request.get_json()
                theData = objExtractor.PackAll()
                dbe.StoreData(docID, theData)

            # ================= Response Construction =========================
            response_message.description = "Message updated!"
            response_message.msg = API_SUCCESS
            response_message.code = response_codes.message_updated
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        else:
            # ================= Response Construction =========================
            response_message.description = "Failed to update message becauses channel does not exist!"
            response_message.msg = API_FAIL
            response_message.code = response_codes.wrong_channel
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        
    except Exception as e:
        print(e)
        # ================= Response Construction =========================
        response_message.description = "Failed to update message becauses of some internal error!"
        response_message.msg = API_FAIL
        response_message.code = response_codes.internal_error
        # ================= Response Construction =========================
        return jsonify(response_message.msg2dict())

@asyncapimessages.route('/delete/<messageID>/<channelID>/<docID>', methods=['DELETE'])
def deleteMessage(messageID, channelID, docID):
    try:
        # Load Doc
        dbe, objExtractor = Wizard(docID)
        
        if messageID not in objExtractor.messages[channelID]:
            # ================= Response Construction =========================
            response_message.description = "The message you are trying to delete does not exist!"
            response_message.msg = API_FAIL
            response_message.code = response_codes.fail_code
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        if channelID in objExtractor.channels:
            if channelID in objExtractor.messages: 
                del objExtractor.messages[channelID][messageID]
                theData = objExtractor.PackAll()
                dbe.StoreData(docID, theData)

            # ================= Response Construction =========================
            response_message.description = "Message deleted!"
            response_message.msg = API_SUCCESS
            response_message.code = response_codes.message_deleted
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        else:
            # ================= Response Construction =========================
            response_message.description = "Failed to delete message becauses channel does not exist!"
            response_message.msg = API_FAIL
            response_message.code = response_codes.wrong_channel
            # ================= Response Construction =========================
            return jsonify(response_message.msg2dict())
        
    except Exception as e:
        print(e)
        # ================= Response Construction =========================
        response_message.description = "Failed to update message becauses of some internal error!"
        response_message.msg = API_FAIL
        response_message.code = response_codes.internal_error
        # ================= Response Construction =========================
        return jsonify(response_message.msg2dict())
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