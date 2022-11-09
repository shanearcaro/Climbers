import dash
from dash import Dash, html, dcc, callback, Input, Output, State, no_update
import pandas as pd
import plotly.figure_factory as ff
import plotly.express as px
import plotly.graph_objects as go
import requests as r
import json
import datetime as dt
from datetime import date

#------This-is-to-be-replaced-by-api/dmz-functionality-------\
app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Get raw data for graph (formatted raw dictionaries)

friends_list = ['John', 'Frank', 'Eric', 'Bob', 'Dylan', 'Shawn', 'Shane', 'Kobe Bryant', 'Hi', 'Hello', 'Hola', 'Shalom']
friends_div_list = []

for friend in friends_list:
  friends_div_list.append(
    html.Div([friend], style={
            'height':'10%',
            'width':'100%',
            'border-bottom':'1px solid black',
            'display':'block',
            'align-items':'center'
          }),
  )

# App layout
app.layout = html.Div([
  html.Div([
      html.Div([
        html.Div([
          'friends'
        ], style={
          'height':'15%',
          'width':'100%',
          'border-bottom':'1px solid black'
        }),
        html.Div(friends_div_list, 
          style={
            'height':'85%',
            'width':'100%',
            'overflow':'scroll'
          }),
      ], style={
      'margin':'10px',
      'width':'90%',
      'height':'90%',
      'border':'1px solid black',
    }),
  ], style={
      'width':'90%',
      'height':'90%'
    }),
  html.Div([
      'hi'
  ], style={
      'width':'90%',
      'height':'90%'
    }),
], style={
  'display':'grid',
  'grid-template-columns':'auto auto',
  'width':'95%',
  'height':'95vh'
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