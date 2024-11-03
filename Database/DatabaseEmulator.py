import os
import json 
import mysql.connector
from mysql.connector import Error
from datetime import datetime
"""
    This class simulates a database.
    It will store data in specified directory to be accessed and edited.
"""
class DatabaseSim:

    def __init__(self):
        path_separator = os.path.sep
        self.folderPath = os.path.join(os.path.dirname(__file__), 'Database_Data')
        # MySQL connection settings
        self.host = 'db'
        self.database = 'web_of_things'
        self.user = 'wot_user'
        self.password = 'web_of_things_mysql_db@'
    
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
        Store specified document in database.
        If document with the same name exists it will be deleted.

        params:
            dataToWrite: python dictionary 
    """
    def StoreData(self, docID, dataToWrite):
        try:
            # Establish MySQL connection
            connection = mysql.connector.connect(
                host=self.host,
                database=self.database,
                user=self.user,
                password=self.password
            )
            
            if connection.is_connected():
                cursor = connection.cursor()

                # Prepare SQL query to insert the data
                insert_query = """
                    INSERT INTO thing_description (device_name, specification_type, td, date_inserted)
                    VALUES (%s, %s, %s, %s)
                """
                device_name = "dht22_sensor_mqtt_protocol"
                specification_type = "asyncapi"
                td_json = json.dumps(dataToWrite)
                date_inserted = datetime.now()

                # Execute the query
                cursor.execute(insert_query, (device_name, specification_type, td_json, date_inserted))

                # Commit the transaction
                connection.commit()

                print(f"Document {docID} stored successfully in MySQL.")

        except Error as e:
            print(f"Error while connecting to MySQL: {e}")
        finally:
            if connection.is_connected():
                cursor.close()
                connection.close()