import os
import sys

import dash
from dash import Input, Output, State, callback, dcc, html, no_update

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
import util

dash.register_page(
    __name__,
    path='/'
)

home = html.Div(id='location')

@dash.callback(
    Output('location', 'children'),
    Input('session-userid', 'data')
)
def home(userid):
    if userid > 0:
        return dcc.Location(pathname='/social', id='redirect')
    else:
        return dcc.Location(pathname='/login', id='redirect')
    
def layout():
    return home