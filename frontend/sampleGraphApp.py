import datetime as dt
import json
from datetime import date

import pandas as pd
import plotly.express as px
import plotly.figure_factory as ff
import plotly.graph_objects as go
import requests as r
from dash import Dash, Input, Output, State, callback, dcc, html, no_update

#------This-is-to-be-replaced-by-api/dmz-functionality-------\
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
#------------------------------------------------------------   

hours = [(dt.time(i).strftime('%I %p')) for i in range(24)]     
# Get pandas dataframe for figure
df = pd.DataFrame(lowest_areas_formatted)

fig = px.scatter_mapbox(df, lat="lat", lon="lng",
                  size_max=14, 
                  zoom=7,
                  hover_data=["areaName"])

fig.update_layout(mapbox_style="open-street-map", hoverlabel=dict(
        bgcolor="white",
        font_size=16,
    ))

fig.update_traces(
    marker=dict(size=10),
    selector=dict(mode="markers"),
)

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
        html.Div([
          'Select an area on the map'
        ], id='area', style={
            'font-size':'30px'
          }),
        html.Div([
          
        ], id='lat'),
        html.Div([
          
        ], id='lon'),
        html.Div('Select a date', style={
            'margin-top':'20px'
          }),
        dcc.DatePickerSingle(
          id='my-date-picker-single',
          min_date_allowed=dt.datetime.now()-dt.timedelta(days=1),
          max_date_allowed=dt.datetime.now()+dt.timedelta(days=365),
          placeholder='Select date',
        ),
        html.Div('Select a time', style={
            'margin-top':'20px'
          }),
        dcc.Dropdown(hours, id='time-dropdown',
          style={
            'width': '50%'
          }),
        html.Div(id='output-container-date-picker-single'),
        html.Button('Schedule', id='submit')
    ], id='click', style={
        'height':'70%',
        'min-width':'70%',
        'border':'1px solid darkgray',
        'margin':'auto',
        'padding':'10px'
    })
], style={
    'display':'grid',
    'grid-template-columns':'50vw 50vw',
    'width':'100%',
    'height':'100vh',
    'margin':'auto',
})

@app.callback(
    Output('area', 'children'),
    Output('lat', 'children'),
    Output('lon', 'children'),
    Input('map', 'clickData'),
    prevent_initial_call=True
)
def click(clickdata):
    print(clickdata)
    points = clickdata['points'][0]
    print(points['lat'])
    print(points['lon'])
    print(points['customdata'][0])
    print(points)
    return points['customdata'][0], points['lat'], points['lon']
  
@app.callback(
    Output('output-container-date-picker-single', 'children'),
    Input('my-date-picker-single', 'date'),
    State('time-dropdown', 'value'),
    prevent_initial_call=True)
def update_output(date_value, time):
    string_prefix = 'You have selected: '
    if time is not None:
      if date_value is not None:
          date_object = date.fromisoformat(date_value)
          date_string = date_object.strftime('%B %d, %Y')
          return string_prefix + date_string + ' at ' + time

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)