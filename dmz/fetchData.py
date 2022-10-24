import requests as r
import json

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

#This is a dictionary containing the entire response
#Do with it what you will
jdict = json.loads(myreq.text)

#print(jdict)