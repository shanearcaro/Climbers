import os
import sys

import dash
from dash import Input, Output, State, callback, dcc, html, no_update

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
import util

#Dash requires pages to be registered
dash.register_page(
    __name__, 
    title='Reset Password', 
    path='/reset'
)

# Layout: Reset Form
resetform = [
    #Fun image
    html.Img(src=util.format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),

    html.H1("Reset Password", className='page-title'),

    #Actual form area
    html.Div('Username', className='label'),
    dcc.Input('', className='input', id='user'),
    
    #Button to toggle between login and signup
    html.Div(
        dcc.Link(
            "Back to Login", 
            href='/login'
        ), 
        id='signin-toggle', 
        className='login-signup-toggle'
    ),

    # Button to toggle between login and signup
    html.Div(
        dcc.Link(
            "Don't Have an Account?", 
            href='/signup'
        ), 
        id='login-toggle', 
        className='login-signup-toggle'
    ),

    #Submit button
    html.Button('Continue', id='submit-val', className='loginbutton'),
]

# Layout: Signup page
resetpage = html.Div(id='layout', className='layout', children=[
        # Entire signup form, with logo, inputs, and buttons
        html.Div(children=resetform, id='form-area', className='form-area'),

        # Empty Div for logic reasons
        html.Div(id='hidden-reset-div')
    ]
)
@dash.callback(
    Output('hidden-reset-div', 'children'),
    Input('submit-val', 'n_clicks'),
    State('user', 'value'),
    prevent_initial_call=True
)
def requestReset(_, username):
    return html.Div("Password reset")

def layout():
    return resetpage