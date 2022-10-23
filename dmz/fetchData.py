import requests as r
import json

query = ''' query {
    areas {
        area_name
        uuid
    }
}'''

myreq = r.post('https://stg-api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json={'query': query},
            )

print(myreq.text)