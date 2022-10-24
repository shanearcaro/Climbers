import requests as r
import json, yaml

#THIS QUERY IS INCOMPLETE
#IT WILL RETURN SOME BROKEN DATA THAT WILL NEED TO BE FILTERED OUT
#IT BOILS DOWN TO REMOVING ANY AREAS WITH NO CLIMBS
query = ''' query {
  areas(filter: {area_name: {match: "New Jersey"}}) {
    children {
      children {
        children {
          areaName
          climbs {
            name
          }
          metadata {
            lat
            lng
          }
        }
      }
    }
  }
}
'''

myreq = r.post('https://api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json={'query': query},
            )

# This is a dictionary containing the entire response
# Do with it what you will
# 
# To edog: >:) Challenge accepted
jdict = json.loads(myreq.text)

# print(jdict)

# Pretty print of raw dictionary:
# print(yaml.dump(jdict, allow_unicode=True, default_flow_style=False))

area_count = 0

areas = jdict['data']['areas'][0]['children']
for area in areas:
  for child in area['children']:
    # print(str(child['children']) + '\n')
    for areaName in child['children']:
      area_count += 1;
      print(areaName)

print(f'\n Area count: {area_count}')


