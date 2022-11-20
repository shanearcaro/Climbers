import json

import requests as r

q_single_area = '''
fragment climbData on Area{
  totalClimbs
  climbs {
    id
    fa
    yds  
    content{
      description
      protection
    }
  }
}

query ClimbsInArea{
  area(uuid: ""){
    ...climbData
  }
}
'''