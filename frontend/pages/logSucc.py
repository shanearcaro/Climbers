import os
import sys
import json

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
    html.Div(id="chat-table"),
    html.Div(id='chat-id', style={'display': 'none'}),
    dcc.Interval(
        id='interval',
        interval=1*10000, # in milliseconds
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
        response = util.createChatRequest(1, "time", userid)
    except:
        return html.Div('An error occurred while running the createGroup script')
    r_c = response['returnCode']

    # 3 - 5 are failure errors
    if r_c == 3 or r_c == 4 or r_c == 5:
        return html.Div(response['message'], style={'color': 'red'}), {'display': 'none'}, '-1'
    return [
        html.Div("Area: 1 Time: time", id='messages-area', className='label'),
        html.Div(children=[], id='messages-table', style={'width': '700px', 'height': '300px', 'overflowY': 'scroll'}),
        dcc.Input('', className='input', id='message-input'),
        html.Button("Send Message", id="send-message"),
        html.Div(id='error-text'),
        ], {'display': 'none'}, response['chatid']

# Populate Chat
@dash.callback(
    Output('messages-table', 'children'),
    Input('interval', 'n_intervals'),
    Input('message-input', 'value'),
    State('messages-table', 'children'),
    Input('userid-text', 'value'),
    State('chat-id', 'value'),
)
def load(_, n_clicks, children, userid, chatid):
    response = None
    try:
        response = util.getMessagesRequest(userid, chatid)
    except:
        return html.Div('An error occurred while running the createMessage script')
    code = response['returnCode']

    data = response['data']

    # if code == 1:
    #     children.append(html.P(response['message']))
    # else:
    #     children.append(html.P(response['message']))

    newChildren = []
    for index in range(0, len(data) - 1, 4):
        user = data[index]
        message = data[index + 1]
        timestamp = data[index + 2]
        username = data[index + 3]


        regular = {'fontSize': '1rem', 'padding': '0px', 'margin': '0px', 'alignSelf': 'right'}
        if user == userid:
            regular['alignSelf'] = 'left'
        small = regular.copy()
        small['fontSize'] = '0.5rem'
        newChildren.append(html.Div(children=[
            html.P(username, style=regular),
            html.P(message, style=regular),
            html.P(timestamp, style=small)
        ], style={'display': 'flex', 'flexDirection': 'column', 'justifyContent': 'center', 'padding': '0px', 'margin': '0px'}))
        
    return newChildren
    # if code == 1:
    #     for index in data:
    #         children.append(html.P("MESSAGE"))
    # else:
    #     children.append(html.P(response['data']))
    # return children
# @dash.callback(
#     Output('messages-table', 'children'), 
#     Output('message-input', 'value'),
#     Input('send-message', 'n_clicks'), 
#     # Input('chat-table', 'children'),
#     State('message-input', 'value'),
#     State('messages-table', 'children'),
#     prevent_initial_call=True
# )
# def load(n_clicks, message, children):
#     if message != '':
#         children.append(html.P(message))
#     return children, ''

# Send a message
@dash.callback(
    Output('error-text', 'children'),
    Output('message-input', 'value'),
    Input('send-message', 'n_clicks'),
    Input('userid-text', 'value'),
    State('chat-id', 'value'),
    State('message-input', 'value'),
    prevent_initial_call=True
)
def send_message(_, userid, groupid, message):
    response = None
    try:
        response = util.createMessageRequest(userid, groupid, message)
    except:
        return html.Div('An error occurred while running the createMessage script'), ''
    return html.Div(response['message']), ''

# Display users


# Set page layout
def layout():
    return success