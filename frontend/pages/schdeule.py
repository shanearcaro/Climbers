import datetime as dt
import json
import os
import sys
from datetime import date

import pandas as pd
import plotly.express as px
import plotly.figure_factory as ff
import plotly.graph_objects as go
import requests as r
from dash import Dash, Input, Output, State, callback, dcc, html, no_update

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
from util import *

## This is where we call to the DMZ for data
lowest_areas_formatted = None

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

# Page layout
Dash.layout = html.Div([
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

@callback(
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
  
@callback(
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