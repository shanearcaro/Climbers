import requests as r
import json
from query import querystring
from pprint import pprint

myreq = r.post('https://api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json={'query': querystring},
            )

# This is a dictionary containing the entire response
jdict = json.loads(myreq.text)
pprint(jdict)