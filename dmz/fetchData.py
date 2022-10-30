import requests as r
import json
from query import querystring
from pprint import pprint

# This is a recursive function that will return a dictionary 
# of the area names and lat/lng coordinates of the bottom-most 
# areas in the response
def get_lowest_area_dicts(data):
    area_dicts = []
    for area_dict in data:
        if area_dict['children'] == []:
            area_dicts.append(area_dict)
        else:
            area_dicts.extend(get_lowest_area_dicts(area_dict['children']))
    return area_dicts

myreq = r.post('https://api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json={'query': querystring},
            )

# This is a dictionary containing the entire response
responseDictionary = json.loads(myreq.text)
responseDictionary = responseDictionary['data']['areas'][0]['children']

#pprint(responseDictionary)

pprint(get_lowest_area_dicts(responseDictionary))