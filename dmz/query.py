querystring = ''' 

fragment locationData on Area{
  metadata {
    lat
    lng
  }
}

query AreasWithLatLng{
  areas(filter: {area_name: {match: "New Jersey"}}){
    children {
      areaName
      ...locationData
      children {
        areaName
        ...locationData
        children {
          areaName
          ...locationData
          children {
            areaName
            ...locationData
            children{
              areaName
              ...locationData
              children{
              	areaName
              	...locationData
                children{
              	  areaName
              	  ...locationData
                  children{
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
# fragment climbData on Area{
# 	totalClimbs
#   climbs {
#     id
#   }
# }