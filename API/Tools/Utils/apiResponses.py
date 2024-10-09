"""
    This file represents the apropriate responses of the Thesis API.
"""

class API_Response_Message:
    def __init__(self):
        self.description = "None"
        self.msg = "None"
        self.code = "0"

    def msg2dict(self):
        m2d = {
            "description":self.description,
            "msg":self.msg,
            "code":self.code
        }

        return m2d


class API_Response_Codes:

    def __init__(self):
        # ============== success ============== 
        self.success_code = "0"
        self.message_updated = "1"
        self.message_deleted = "2"
        # ============== fail ============== 
        self.fail_code = "-1"
        self.wrong_channel = "-3"
        self.wrong_document = "-2"

        # System errors 3 digits
        self.internal_error = "-100" # exception error

API_SUCCESS = "success"
API_FAIL = "fail"
response_message = API_Response_Message()
response_codes = API_Response_Codes()