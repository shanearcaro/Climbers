import dash
from dash import Dash, html, dcc, callback, Input, Output, State, no_update
import pandas as pd
import plotly.figure_factory as ff
import plotly.express as px
import plotly.graph_objects as go
import requests as r
import json

app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Get raw data for graph (formatted raw dictionaries)

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

querystring2 = '''
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

lowest_areas_formatted = []
for area_dict in lowest_areas:
  temp_dict = {'uuid': area_dict['uuid'],
                     'areaName': area_dict['areaName'],
                     'lat': area_dict['metadata']['lat'],
                     'lng': area_dict['metadata']['lng'],
                     'children': area_dict['children']}
  lowest_areas_formatted.append(temp_dict)
        
# Get pandas dataframe for figure
df = pd.DataFrame(lowest_areas_formatted)

fig = px.scatter_mapbox(df, lat="lat", lon="lng",
                  size_max=14, 
                  zoom=7)

fig.update_layout(mapbox_style="open-street-map")

# App layout
app.layout = html.Div([
    dcc.Graph(figure=fig, id='map',
            config={
        'displayModeBar': False
    },
              style={
        'height':'90%',
        'min-width':'100%',
        'margin':'auto'
    }),
    html.Div([
        'hi'
    ], id='click', style={
        'height':'70%',
        'min-width':'70%',
        'border':'1px solid darkgray' ,
        'margin':'auto'
    })
], style={
    'display':'grid',
    'grid-template-columns':'50vw 50vw',
    'width':'100%',
    'height':'100vh',
    'margin':'auto',
})

@app.callback(
    Output('click', 'children'),
    Input('map', 'clickData'),
)
def click(clickdata):
    print(clickdata)
    return str(clickdata)

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)