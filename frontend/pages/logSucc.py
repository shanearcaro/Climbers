import os
import sys
import pipes
import math

from datetime import datetime

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
success = html.Div(id='main-content', children=[
    html.Div(id='userid-text', style={'display': 'none'}),
    html.Button('Create Chat', id='create-chat', className='create-button'),
    html.Div(id='chat-table'),
    html.Div(id='error-text'),
    html.Div(id='chat-id', style={'display': 'none'}),
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
    Output('chat-table', 'children'),
    Output('create-chat', 'style'),
    Output('chat-id', 'value'),
    Input('userid-text', 'value'),
    prevent_initial_call=True
)
def join(userid):
    response = None
    try:
        # TODO: Implement dynamic area and time elements with the graph area gui
        # Need to fix input, both inputs will be broken with certain characters and spaces
        response = util.createChatRequest("Mountains", "9AM", userid)
    except:
        return html.Div('An error occurred while running the createGroup script')
    r_c = response['returnCode']

    # 3 - 5 are failure errors
    if r_c == 3 or r_c == 4 or r_c == 5:
        return html.Div(response['message'], style={'color': 'red'}), {'display': 'none'}, '-1'
    return [
        html.Div(children=[
            html.H1(response['area']),
            html.P(response['time'])
        ], id='messages-area', className='label'),
        html.Table(children=[], id='messages-table'),
        html.Div(id='input-area', 
        children=[
            dcc.Input('', className='input', id='message-input', debounce=True, type='text'),
        ])
    ], {'display': 'none'}, response['chatid']

# Populate Chat
@dash.callback(
    Output('messages-table', 'children'),
    Input('interval', 'n_intervals'),
    Input('message-input', 'value'),
    State('messages-table', 'children'),
    Input('userid-text', 'value'),
    State('chat-id', 'value'),
    prevent_initial_call=True
)
def load(_, n_clicks, children, userid, chatid):
    response = None
    try:
        response = util.getMessagesRequest(userid, chatid)
    except:
        return html.Div('An error occurred while running the createMessage script')

    data = response['data']

    # Create new child structure with messages
    # Terribly inefficent
    newChildren = []
    for index in range(0, len(data) - 1, 4):
        user = data[index]
        message = data[index + 1]
        timestamp = data[index + 2]
        username = data[index + 3]

        classes = "message-container message-"
        classes = classes + "right" if user == userid else classes + "left message-blocked"
        newChildren.append(html.Tr(children=[
            html.Td(children=[
                html.P(username, className='message-element message-username'),
                html.P(message, className='message-element message-content'),
                html.P(getTimestamp(timestamp), className='message-element message-timestamp')
            ], className=classes)
        ], className="message-row"))
        
    return newChildren

# Send a message
@dash.callback(
    Output('error-text', 'children'),
    Output('message-input', 'value'),
    Input('message-input', 'value'),
    State('userid-text', 'value'),
    State('chat-id', 'value'),
    prevent_initial_call=True
)
def send_message(message, userid, groupid):
    # # Fixes userid update bug
    if message == '':
        return no_update, no_update
    message = pipes.quote(message)
    # If server fails to send message, retry
    response = None
    retryLimit = 10
    trys = 0

    while trys < retryLimit:
        try:
            response = util.createMessageRequest(userid, groupid, message)
        except:
            trys += 1
            continue
        break
    if response:
        return html.Div(response['message']), ''
    return html.Div('[System] Message failed to send'), ''

def getTimestamp(timestamp):
    format = "%Y-%m-%d %H:%M:%S"

    now = datetime.now()
    message_time = datetime.strptime(timestamp, format)

    now_seconds = now.timestamp()
    message_seconds = message_time.timestamp()

    elapsed_time = now_seconds - message_seconds
    elapsed_timestamp = ''

    if elapsed_time < math.pow(60, 1):
        return str(int(elapsed_time)) + " seconds ago"
    elif elapsed_time < math.pow(60, 2):
        return str(int(elapsed_time / math.pow(60, 1))) + " minutes ago"
    elif elapsed_time < math.pow(60, 3):
        return str(int(elapsed_time / math.pow(60, 2))) + " hours ago"
    elif elapsed_time < math.pow(60, 4):
        return str(int(elapsed_time / math.pow(60, 3))) + " days ago"
    elif elapsed_time < math.pow(60, 5):
        return str(int(elapsed_time / math.pow(60, 4))) + " weeks ago"

# Display users


# Set page layout
def layout():
    return success