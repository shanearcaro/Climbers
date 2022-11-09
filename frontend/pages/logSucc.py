import os
import sys

import dash
from dash import Input, Output, State, callback, dcc, html, no_update

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
import util

dash.register_page(
    __name__, 
    title='Success!', 
    path='/logSucc'
)


# Layout: Success (Temporary)
success = html.Div( children=[
    html.Div('Success!'),
    html.Div(id='userid-text', style={'display': 'none'}),
    html.Button('Create Chat', id='create-chat', className='create-button'),
    html.Table(id="chat-table"),
    dcc.Interval(
        id='interval',
        interval=1*1000, # in milliseconds
        n_intervals=0
    )
])

chats = html.Div(children=[
    html.Div('SWITCHED')
])

# Get userid and store in hidden div
@dash.callback(
    Output('userid-text', 'children'),
    [Input('session-userid', 'data')])
def on_data(data):
    id = data
    return html.Div(id)

@dash.callback(
    Output('chat-table', 'children'),
    Input('create-chat', 'n_click'),
    Input('userid-txt', 'data')
)
def create_group(n_click, userid):
    response = util.createChatRequest(n_click, "time", userid)
    return html.Div(response)


def layout():
    return success