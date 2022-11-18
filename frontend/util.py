import base64
import json
import subprocess

# Dash requires a special image format
def format_img(img):
    b64encoded_img=base64.b64encode(open(f'assets/{img}', 'rb').read())
    return f'data:image/png;base64,{b64encoded_img.decode()}'

def run_php_script(path, args):
    '''Run a PHP script and return the output'''

    # Set up the command, and arguments if it has any
    shellstr = f"php {path}"
    if args:
        for arg in args:
            shellstr += f" {arg}"

    # Run the script
    proc = subprocess.Popen(
        shellstr, shell=True, stdout=subprocess.PIPE)

    # Get output from php script
    response = proc.stdout.read()

    # Decode bytes to string, return raw string
    return response.decode('utf-8')

#The following functions are wrappers for the PHP scripts

def getLowestAreas():
    #This has no arguments because it's just a request for the lowest areas
    #in NJ hard coded into the PHP script
    return json.loads(run_php_script("apiRequest.php", []))

def createLog(message=''):
    '''Sends a log request to the rabbitmq server which gets sent to 
    all other clients through a fanout exchange. Each client's then 
    creates their own log file locally'''
    return run_php_script("../logging/logPublish.php", [message])

def loginRequest(username='', password=''):
    return json.loads(
        run_php_script("loginRequest.php", [username, password]))
        
def signupRequest(username='', email='', password=''):
    return json.loads(
        run_php_script("userAddRequest.php", [username, email, password]))

def createChatRequest(area='', time='', userid=''):
    return json.loads(
        run_php_script("createChatRequest.php", [area, time, userid]))

def createMessageRequest(userid='', chatid='', message=''):
    return json.loads(
        run_php_script("createMessageRequest.php", [userid, chatid, message]))

def getMessagesRequest(userid='', chatid=''):
        return json.loads(
            run_php_script("getMessagesRequest.php", [userid, chatid]))

def getBlockedUsers(userid=''):
        return json.loads(
            run_php_script("getBlockedUsers.php", [userid]))

def getChatrooms(userid=''):
        return json.loads(
            run_php_script("getChatrooms.php", [userid]))

def scheduleRequest():
    return #run_php_script("../scheduleRequest.php", [])
