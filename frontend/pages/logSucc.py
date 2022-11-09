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
    html.Div(id='userid-text', style={'display': 'none'}),
    html.Button('Create Chat', id='create-chat', className='create-button'),
    html.Table(id="chat-table"),
    dcc.Interval(
        id='interval',
        interval=1*1000, # in milliseconds
        n_intervals=0
    )
])

# Pull the userid session
@dash.callback(
    Output('userid-text', 'value'),
    Input('create-chat', 'n_clicks'),
    State('session-userid', 'data'),
    prevent_initial_call=True
)
def setid(_, data):
    return data

# Join a chat
@dash.callback(
    [
        Output('chat-table', 'children'),
        Output('create-chat', 'style')
    ],
    Input('userid-text', 'value'),
    prevent_initial_call=True
)
def join(userid):
    response = None
    try:
        # TODO: Implement dynamic area and time elements with the graph area gui
        response = util.createChatRequest(1, "time", userid)
    except:
        return html.Div('An error occurred while running the createGroup script')
    return [
        html.Div("Area: 1 Time: time", id='messages-area', className='label'),
        html.Table("CHAT", id='messages-table', style={'width': '100px', 'height': '100px', 'overflow-y': 'scroll'}),
        dcc.Input('', className='input', id='message-input'),
        html.Button("Send Message", id="send-message")
        ], {'display': 'none'}

# User has joined a chat

# Set page layout
def layout():
    return success