import requests as r
import json

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

query SingleArea{
  area(uuid: ""){
    ...climbData
  }
}
'''