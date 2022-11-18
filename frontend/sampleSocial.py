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
blocked_list = ['Sonjay']

# Generate people list (friends/blocked)
def create_people_div_list(people_list):
    people_div_list = []
    friend_item_style = {
                  'height':'10%',
                  'width':'100%',
                  'border-bottom':'1px solid black',
                  'display':'block',
                  'align-items':'center'}
    for person in people_list:
        if people_list == friends_list:
          people_div_list.append(
            html.Div([
              person,
              html.Button(['Chat'], id=f'{person}-chatbtn'),
              html.Button(['Block'], id=f'{person}-blockbtn')
              ], style=friend_item_style),
          )
        else:
          people_div_list.append(
            html.Div([
              person,
              html.Button(id=f'{person}-chatbtn', style={'display':'none'}),
              html.Button(['Unblock'], id=f'{person}-blockbtn')
              ], style=friend_item_style),
          )
    return people_div_list

# App layout
app.layout = html.Div([
  html.Div([
    html.Div([
      html.Div('Friends List', 
        style={
          'background-color':'#2f4f04',
          'height':'10%',
          'color':'white'
      }),
      html.Div(create_people_div_list(friends_list),
        id='friends-list',
        style={
          'overflow-y':'scroll',
          'width':'100%',
          'height':'90%',
          'max-height':'90%'
        }
      )
    ],
      id='list-type', 
      style={
        'height':'50vh',
        'max-height':'60%',
        'width':'100%',
        'border':'1px solid black',
    }),
    html.Div([
      html.Div('Blocked List', 
        style={
          'background-color':'#2f4f04',
          'height':'10%',
          'color':'white'
      }),
      html.Div(create_people_div_list(blocked_list),
        id='blocked-list',
        style={
          'overflow-y':'scroll',
          'width':'100%',
          'height':'90%',
          'max-height':'90%'
        }
      )
    ], 
      style={
        'margin-top':'20px',
        'height':'30vh',
        'max-height':'30%',
        'width':'100%',
        'border':'1px solid black',
    }),
  ], style={
    'margin':'auto',
    'max-width':'90%',
    'min-width':'90%',
    'max-height':'90%',
    'min-height':'90%',
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

# Early stages of sending message
@app.callback(
  Output('messages', 'children'),
  Input('send_message_btn', 'n_clicks'),
  Input('send_message_field', 'n_submit'),
  State('send_message_field', 'value'),
  prevent_initial_call=True
)
def send_message(messages, button, field_submit):
    
    return f'{messages}' + f'{field_submit}'

# Chat button callback
@app.callback(
  Output('recipient', 'children'),
  [Input('{}-chatbtn'.format(people), 'n_clicks') for people in friends_list],
  prevent_initial_call=True
)
def set_recipient(*args):
    changed_id = [p['prop_id'] for p in dash.callback_context.triggered][0]
    # Output chat (current session, session clicked)
    
    return changed_id.split('-')[0]

@app.callback(
  Output('friends-list', 'children'),
  Output('blocked-list', 'children'),
  [Input('{}-blockbtn'.format(people), 'n_clicks') for people in (friends_list + blocked_list)],
  prevent_initial_call=True
)
def block_unblock(*args):
  changed_id = [p['prop_id'] for p in dash.callback_context.triggered][0]
  button_id = changed_id.split('.')[0]
  name = button_id.split('-')[0]
  # Getting ID

  # Guard against empty input
  if changed_id == 'x':
    return no_update, no_update

  if name in friends_list:
    friends_list.remove(name) # SQL move to block
    blocked_list.append(name) 
  else:
    blocked_list.remove(name) # SQL move to friends
    friends_list.append(name)

  # create_people_div_list(getFriends()/getBlocked())
  return create_people_div_list(friends_list), create_people_div_list(blocked_list)

#[Input('{}-blockbtn'.format(people), 'n_clicks') for people in current_list],



if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)