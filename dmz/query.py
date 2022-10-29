querystring = ''' fragment climbData on Area{
	totalClimbs
  climbs {
    id
  }
}

fragment locationData on Area{
  metadata {
    lat
    lng
  }
}

query {
  areas(filter: {area_name: {match: "New Jersey"}}) {
    children {
      areaName
      children {
        areaName
        ...locationData
        ...climbData
        children {
          areaName
          ...locationData
          ...climbData
          children {
            areaName
            ...locationData
        	...climbData
            children{
              areaName
              ...locationData
        	  ...climbData
              children{
              	areaName
              	...locationData
        		...climbData
                children{
              		areaName
              		...locationData
        			...climbData
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