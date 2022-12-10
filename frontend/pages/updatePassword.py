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
    title='Update Password', 
    path='/update-password'
)

# Layout: Reset Form
resetform = [
    #Fun image
    html.Img(src=util.format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),

    html.H1("Update Password", className='page-title'),

    #Actual form area
    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='pass', type='password'),

    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='conf_pass', type='password'),

    #Empty Div for logic reasons
    html.Div(id='hidden-update-div', style={
        'color':'red',
        'padding-bottom':'10px'
    }),
    
    #Button to toggle between login and signup
    html.Div(
        dcc.Link(
            "Back to Login", 
            href='/login'
        ), 
        id='signin-toggle', 
        className='login-signup-toggle'
    ),

    #Submit button
    html.Button('Continue', id='submit-val', className='loginbutton'),
]

# Layout: Signup page
resetpage = html.Div(id='layout', className='layout', children=[
        # Entire signup form, with logo, inputs, and buttons
        html.Div(children=resetform, id='form-area', className='form-area')
    ]
)
@dash.callback(
    Output('hidden-update-div', 'children'),
    Input('submit-val', 'n_clicks'),
    State('pass', 'value'),
    State('conf_pass', 'value'),
    State("session-userid", "data"),
    prevent_initial_call=True
)
def requestReset(_, password, confirm_password, userid):
    # Make sure both passwords match
    if password != confirm_password:
        return html.Div("Both fields must match to update your password.")

    # Attempt to reset the password
    response = util.sendRequest(parameters=["reset_password", userid, password])

    # If the request was successful
    if response.get("returnCode") > 0:
        return html.Div("Password successfully updated")
    else:
        return html.Div("Password failed to update. Try again in a couple of minutes.")

def layout():
    return resetpage