import os
import sys
import pipes
import math
import numpy as np

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
    html.Div(id='content-area', children=[
        html.Div(id='chatroom-table', className='hidden'),
        html.Div(id='chat-table', className='hidden'),
        # html.Div(id='error-text'),
        html.Div(id='friends-table', className='hidden'),
        html.Div(id='chat-id', style={'display': 'none'})
    ]),
    dcc.Interval(
        id='interval',
        interval=1*1000, # in milliseconds
        n_intervals=0
    )
])

# Pull the userid session
@dash.callback(
    Output('userid-text', 'value'),
    Output('chatroom-table', 'className'),
    Output('chat-table', 'className'),
    Output('friends-table', 'className'),
    Input('create-chat', 'n_clicks'),
    State('session-userid', 'data'),
    State('content-area', 'className'),
    prevent_initial_call=True
)
def setid(_, data, className):
    return data, '', '', ''

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
    # try:
        # TODO: Implement dynamic area and time elements with the graph area gui
        # Need to fix input, both inputs will be broken with certain characters and spaces
    response = util.createChatRequest("Mountains", "9AM", userid)
    # except:
        # return html.Div('An error occurred while running the createGroup1 script')
    r_c = response['returnCode']

    # 3 - 5 are failure errors
    if r_c == 3 or r_c == 4 or r_c == 5:
        return html.Div(response['message'], style={'color': 'red'}), {'display': 'none'}, '-1'
    return [
        html.Div(children=[
            html.H1(response['area'], id='area-title'),
            html.P(response['time'], id='area-time')
        ], id='messages-area'),
        html.Table(children=[html.Tbody(children=[], id='table-body')], id='messages-table'),
        html.Div(id='input-area', 
        children=[
            
            dcc.Input('', className='input', id='message-input', debounce=True, type='text'),
        ])
    ], {'display': 'none'}, response['chatid']

# Populate Chat
@dash.callback(
    Output('table-body', 'children'),
    Input('interval', 'n_intervals'),
    Input('message-input', 'value'),
    Input('userid-text', 'value'),
    State('table-body', 'children'),
    State('chat-id', 'value'),
    prevent_initial_call=True
)
def load(_, n_clicks, userid, children, chatid):
    response = None
    blocks = None
    try:
        response = util.getMessagesRequest(userid, chatid)
        blocks = util.getBlockedUsers(userid)
    except:
        return html.Div('An error occurred while running the startup script')

    data = response['data']
    blocked_users = np.squeeze(blocks['data'])

    # Create new child structure with messages
    # Terribly inefficent
    newChildren = []
    for index in range(0, len(data) - 1, 4):
        user = data[index]
        message = data[index + 1]
        timestamp = data[index + 2]
        username = data[index + 3]

        classes = "message-container message-"
        classes = classes + "right" if user == userid else classes + "left"
        if user in blocked_users:
            classes += " message-blocked"
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
    # Output('error-text', 'children'),
    Output('message-input', 'value'),
    Input('message-input', 'value'),
    State('userid-text', 'value'),
    State('chat-id', 'value'),
    prevent_initial_call=True
)
def send_message(message, userid, groupid):
    if message == '':
        # return no_update, no_update
        return no_update
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
        return ''
    return '[System] Message failed to send'

def getTimestamp(timestamp):
    # Calculate elapsed time for message sent to now
    format = "%Y-%m-%d %H:%M:%S"
    now = datetime.now()
    message_time = datetime.strptime(timestamp, format)

    now_seconds = now.timestamp()
    message_seconds = message_time.timestamp()

    elapsed_time = int(now_seconds - message_seconds)
    # Format return
    time_units = {'second': 60, 'minute' : 60, 'hour': 24, 'day': 7, 'week': 52, 'year': 10}

    for i, unit in enumerate(list(time_units.keys())):
        # Length of unit and previous unit in seconds
        unit_len = np.prod(list(time_units.values())[0: i + 1])
        punit_len = unit_len / time_units[unit]

        # Elapsed time in unit format
        elapsed_unit = math.floor(elapsed_time / punit_len)

        if elapsed_time < unit_len:
            postfix = ' ago' if elapsed_unit == 1 else 's ago'
            return f'{elapsed_unit} {unit}{postfix}'

# Display users
@dash.callback(
    Output('chatroom-table', 'children'),
    Input('userid-text', 'value'),
    prevent_initial_call=True
)
def load_chatgroups(userid):
    response = None
    # try:
    response = util.getChatrooms(userid)
    # except:
        # return html.Div('An error occurred while running the startup script')

    chatrooms = response['data']
    children = []
    for room in chatrooms:
        children.append(html.Div(className='chatroom', 
        children=[
            html.P(room[-2]),
            html.P(room[-1])
        ]))

    return children;

# Set page layout
def layout():
    return success