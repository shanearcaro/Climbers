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

# # Get userid and store in hidden div
# @dash.callback(
#     Output('userid-text', 'value'),
#     [Input('session-userid', 'data')])
# def on_data(data):
#     return data

# @dash.callback(
#     Output('chat-table', 'children'),
#     Input('create-chat', 'n_clicks'),
#     Input('userid-text', 'value'),
#     prevent_initial_call=True
# )
# def create_group(n_clicks, userid):
#     # TODO: Needs to be changed to accept areas instead of n_clicks
#     response = util.createChatRequest(userid, "time", userid)
#     return html.Div(response['message'])

@dash.callback(
    Output('userid-text', 'value'),
    Input('create-chat', 'n_clicks'),
    State('session-userid', 'data'),
    prevent_initial_call=True
)
def setid(_, data):
    return data

@dash.callback(
    Output('chat-table', 'children'),
    Input('userid-text', 'value'),
    prevent_initial_call=True
)
def create_group(userid):
    response = None
    try:
        response = util.createChatRequest(userid, "time", userid)
    except:
        return html.Div('An error occurred while running the createGroup script')
    return html.Div(response['message'])


def layout():
    return success