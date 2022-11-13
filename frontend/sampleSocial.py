import dash
from dash import Dash, html, dcc, callback, Input, Output, State
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
    html.Div([
      friend,
      html.Button(['Chat'], id=f'{friend}-chatbtn'),
      html.Button(['Block'], id=f'{friend}-blockbtn')
      ], style={
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
          dcc.Tabs(id="people-list", value='friends', 
              style={
                'background-color':'black'
              },
              children=[
                dcc.Tab(label='Friends List', value='friends',
                        style={
                          'background-color':'lightgray'
                          }),
                dcc.Tab(label='Blocked List', value='blocked',
                        style={
                          'background-color':'red',
                          }),
            ]),
        ], style={
          'height':'15%',
          'width':'100%',
          'border-bottom':'1px solid black',
          'background-color':'#2f4f04',
          'color':'white',
          'font-weight':'bold',
          'font-size':'30px',
          'text-align':'center',
        }),
        html.Div(friends_div_list, 
          style={
            'height':'85%',
            'width':'100%',
            'overflow':'scroll'
          }),
      ], style={
      'margin':'auto',
      'width':'90%',
      'height':'90%',
      'border':'1px solid black',
    }),
  html.Div([
      html.Div([
        'Choose a recipient'
      ], id='recipient', style={
        'height':'10%',
        'width':'100%',
        'border-bottom':'1px solid black',
        'background-color':'#2f4f04',
        'color':'white',
        'font-weight':'bold',
        'font-size':'30px',
      }),
      html.Div([
        html.Div([
          
        ], id='messages',
                 style={
                   'overflow':'auto',
                   'max-height':'80%'
                 })
      ], id='conversation',
          style={
        'width':'100%',
        'height':'80%',
        'border-bottom':'1px solid black',
      }),
      html.Div([
        dcc.Input(
          id='send_message_field',
          style={
          'height':'50%',
          'width':'80%',
          'margin-left': '10px'
        }),
        html.Button(['Send'], 
          id='send_message_btn', 
          style={
          'height':'50%',
          'width':'10%',
          'margin-left':'5px'
        })
      ], style={
        'width':'100%',
        'height':'10%',
        'display':'flex',
        'align-items':'center'
      })
    ], style={
        'margin':'auto',
        'width':'80%',
        'height':'90%',
        'border':'1px solid black',
    }),
], style={
  'display':'grid',
  'grid-template-columns':'1fr 1fr',
  'width':'95%',
  'height':'95vh',
  'margin':'auto'
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

@app.callback(
  Output('messages', 'children'),
  Input('send_message_btn', 'n_clicks'),
  Input('send_message_field', 'n_submit'),
  State('send_message_field', 'value'),
  prevent_initial_call=True
)
def send_message(messages, button, field_submit):
    
    return f'{messages}' + f'{field_submit}'

@app.callback(
  Output('recipient', 'children'),
  [Input('{}-chatbtn'.format(friends), 'n_clicks') for friends in friends_list],
  prevent_initial_call=True
)
def set_recipient(*args):
    changed_id = [p['prop_id'] for p in dash.callback_context.triggered][0]
    return changed_id.split('-')[0]


if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)