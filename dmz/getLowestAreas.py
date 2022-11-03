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
    if area_dict['children'] == []:
      bottom_area_dicts.append(area_dict)
    else:
      bottom_area_dicts.extend(get_lowest_area_dicts(area_dict['children']))

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
responseDictionary = responseDictionary['data']['areas'][0]['children']

#Then we can run our facny recursive function
lowest_areas = get_lowest_area_dicts(responseDictionary)

print(lowest_areas)
#pprint(responseDictionary)
#pprint(get_lowest_area_dicts(responseDictionary))

