import os
import json 
"""
    This class simulates a database.
    It will store data in specified directory to be accessed and edited.
"""
class DatabaseSim:

    def __init__(self):
        path_separator = os.path.sep
        self.folderPath = os.path.join(os.path.dirname(__file__), 'Database_Data')
    
    def Test(self):
        print("up and running!")


    """
        Will create a json file
    """
    def CreateDocument(self, docID):
         
        filename = os.path.join(self.folderPath , str(docID)+".json")
        with open(filename, "w") as file:
            pass  # This line is intentionally left empty
    
    """
        Will delete specified document
    """
    def DeleteDocument(self, docID):
        filename = os.path.join(self.folderPath , str(docID)+".json")
        os.remove(filename)
    
    """
        Load data and return a dictionary
    """
    def LoadData(self, docID):
        filename = os.path.join(self.folderPath , str(docID)+".json")
        with open(filename, "r") as f:
            data = json.load(f)

        return data


    """
        Store specified document in data base.
        If document with the same name exists it will be deleted.

        params:
            dataToWrite: python dictionary 
    """
    def StoreData(self, docID, dataToWrite):
        filename = os.path.join(self.folderPath , str(docID)+".json")
         
        with open(filename, "w") as f:
            f.write(json.dumps(dataToWrite, indent=4)) 