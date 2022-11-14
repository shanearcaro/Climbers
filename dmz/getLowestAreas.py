import json

import requests as r

q_nj_areas = ''' 
fragment locationData on Area{
  metadata {
    lat
    lng
  }
}

query NJAreasWithLatLng{
  areas(filter: {area_name: {match: "New Jersey"}}){
    children {
      uuid
      areaName
      ...locationData
      children {
        uuid
        areaName
        ...locationData
        children {
          uuid
          areaName
          ...locationData
          children {
            uuid
            areaName
            ...locationData
            children{
              uuid
              areaName
              ...locationData
              children{
              	uuid
                areaName
              	...locationData
                children{
              	  uuid
                  areaName
              	  ...locationData
                  children{
              	    uuid
                    areaName
              	    ...locationData
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
'''

def get_lowest_area_dicts(area_dicts_list):
  '''
  Recursive function that takes a list of nestested dictionaries, 
  and traverses through them to return a list of dictionaries with
  the area names and other data of the bottom-most areas in the response
  '''
  #Setup the return list
  bottom_area_dicts = []

  #Iterate through the passed in list of dictionaries.
  #If the current dictionary has an empty list for the 'children' key,
  #then it is a bottom-most area.
  #Otherwise, the current area is not a bottom-most area, so we need 
  #to recurse and check the areas under (children of) it.
  for area_dict in area_dicts_list:
    if area_dict.get('children') == [] and area_dict.get('uuid') is not None:
      bottom_area_dicts.append(area_dict)
    elif area_dict.get('children') != []:
      bottom_area_dicts.extend(get_lowest_area_dicts(area_dict.get('children')))
    continue

  #Return the list of bottom-most area dictionaries
  return bottom_area_dicts

#Build the request
myreq = r.post('https://api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json={'query': q_nj_areas},
            )

# This is a dictionary containing the entire response
responseDictionary = json.loads(myreq.text)

#So we start by peeling off the outer layers
responseDictionary = responseDictionary['data']['areas']

#Then we can run our facny recursive function
lowest_areas = get_lowest_area_dicts(responseDictionary)

lowest_areas_formatted= []
for area_dict in lowest_areas:
  temp_dict = {'uuid': area_dict.get('uuid'),
               'areaName': area_dict.get('areaName'),
               'lat': area_dict.get('metadata').get('lat'),
               'lng': area_dict.get('metadata').get('lng'),
               'children': area_dict.get('children')}
  lowest_areas_formatted.append(temp_dict)

print(json.dumps(lowest_areas_formatted))