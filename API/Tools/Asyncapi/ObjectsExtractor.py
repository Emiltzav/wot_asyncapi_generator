import json

"""
    This class, given an asyncapi json file will extract all its
    components into a python dictionary. 

    Then users can modify that dictionary.
"""
class AsyncapiExtractor:

    def __init__(self):
        self.asyncDoc = None
        self.ver = ""
        self.info = {}
        self.id = ""
        self.defaultContentType = ""
        self.servers = {}
        self.messages = {}
        self.operations = {}
        self.channels = {}
        self.components = {}
        pass
    
    def ExtractAll(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        try:
            self.ComponentsExtractor()
            self.AsyncapiVersionExtractor()
            self.IdExtractor()
            self.InfoExtractor()
            self.ChannelsExtractor()
            self.ServersExtractor()
            self.MessagesExtractor()
            self.OperationsExtractor()
            self.DefaultContentTypeExtractor()
        except Exception as e:
            print("Error:" ,e)

    """
        Pack all objects into a single asyncapi object (current type: python dictionary).
        Every object of: servers, operations, channels, messages will be moved into components
        and a $ref will be created at the final asyncapi object.
        Then we can use this object to export it to some other functionality like store as a json.
    """
    def PackAll(self):
        
        try:
            # Append messages to componets and create ref in channels object.
            self.components["messages"] = {}
            for key, val in self.messages.items():
                for messKey, messVal in self.messages[key].items():
                    self.components["messages"][messKey] = messVal
            
            for key, value in self.channels.items():
                if key in self.messages:
                    for messageKey, messageValue in self.messages[key].items():
                        if 'messages' in self.channels[key]:
                            self.channels[key]["messages"][messageKey] = {"$ref":"#/components/messages/"+str(messageKey)}
                        else:
                            self.channels[key]["messages"] = {}
                            self.channels[key]["messages"][messageKey] = {"$ref":"#/components/messages/"+str(messageKey)}
            
            # Append channels into components and create $ref
            self.components["channels"] = self.channels
            channelsCopy = {}
            for key, value in self.channels.items():
                channelsCopy[key] = {"$ref":"#/components/channels/"+str(key)}
            
            # Append operations into components and create $ref
            self.components["operations"] = self.operations
            operationsCopy = {}
            for key, value in self.operations.items():
                operationsCopy[key] = {"$ref":"#/components/operations/"+str(key)}
            
            # Append servers into components and create $ref
            self.components["servers"] = self.servers
            serversCopy = {}
            for key, value in self.servers.items():
                serversCopy[key] = {"$ref":"#/components/servers/"+str(key)}
            
            

            packedObject = {
                "asyncapi": "3.0.0",
                "info":self.info,
                "id":self.id,
                "defaultContentType":self.defaultContentType,
                "channels":channelsCopy,
                "operations":operationsCopy,
                "servers":serversCopy,
                "components":self.components
            }
            
            return packedObject
        except Exception as e:
            print("Error", e)
            return {}
    # Converts the whole json into a dictionary. Primitive stage (non ready for edit)
    def Json2DictConv(self, asyncJson):
        try:
            self.asyncDoc = json.loads(asyncJson)
        except : 
            print("Error at conversion")



    # Asyncapi version extraction from dictionary
    def AsyncapiVersionExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if "asyncapi" not in self.asyncDoc: return 
        self.ver = self.asyncDoc["asyncapi"]
    
    def ComponentsExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if "components" not in self.asyncDoc: return
        self.components = self.asyncDoc["components"]

    def IdExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if "id" in self.asyncDoc:
            self.id = self.asyncDoc["id"]

    def DefaultContentTypeExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        try:
            if "defaultContentType" not in self.asyncDoc: return
            self.defaultContentType = self.asyncDoc["defaultContentType"]
        except:
            print("Not default content type found")

    def InfoExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if "info" not in self.asyncDoc: return
        self.info = self.asyncDoc["info"]
    
    def OperationsExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        try:
            if "operations" not in self.asyncDoc: return
            self.operations = self.asyncDoc["operations"]
            if self.components == None:
                return
            
            # for $ref objects replace from components
            for key, value in self.operations.items():
                if '$ref' in value:
                    operationInComponents = self.RefPathResolve(value["$ref"])
                    self.operations[key] = self.components[operationInComponents[0]][operationInComponents[1]]
        except:
            print("error reading operations")
    
    def ServersExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        try:
            if "servers" not in self.asyncDoc: return
            self.servers = self.asyncDoc["servers"]
            if self.components == None:
                return
            
            # for $ref objects replace from components
            for key, value in self.servers.items():
                if '$ref' in value:
                    serversInComponents = self.RefPathResolve(value["$ref"])
                    self.servers[key] = self.components[serversInComponents[0]][serversInComponents[1]]
        except:
            print("Error reading servers!")


    def ChannelsExtractor(self):
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if "channels" not in self.asyncDoc: return
        self.channels = self.asyncDoc["channels"]
        if self.components == None:
            return
        # for $ref objects replace from components
        for key, value in self.channels.items():
            if '$ref' in value:
                channelsInComponents = self.RefPathResolve(value["$ref"])
                self.channels[key] = self.components[channelsInComponents[0]][channelsInComponents[1]]

    def MessagesExtractor(self):
        self.messages = {}
        if self.asyncDoc == None:
            print("Error: define the document first")
            return
        if self.channels == None:
            print("Error: extract channels first")
            return
        try:
            for key, value in self.channels.items():
                if 'messages' in value:
                    # The key of the messages is the ID of the channel
                    self.messages[key] = self.channels[key]['messages']
                    
                    # Extract messages from components
                    for messageKey, messageValues in self.messages[key].items():
                        
                        if '$ref' in messageValues:
                            messagesInComponents = self.RefPathResolve(messageValues["$ref"])
                            self.messages[key][messageKey] = self.components[messagesInComponents[0]][messagesInComponents[1]]          
        except Exception as e:
            print(e)

        

    """
        Resolve the reference path.
        Returns a list:
            element 1: operations, servers, messages etc
            element 2: operation, message, server(etc) ID
    """   
    def RefPathResolve(self, thePath):
        thePath = thePath.split("/")
        return [ thePath[len(thePath)-2], thePath[len(thePath)-1]]