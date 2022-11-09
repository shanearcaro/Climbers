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
    title='Sign Up', 
    path='/signup'
)

# Layout: Signup Form
signupform = [
    #Fun image
    html.Img(src=util.format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),

    #Actual form area
    html.Div('Username', className='label'),
    dcc.Input('', className='input', id='user'),
    html.Div('E-Mail', className='label'),
    dcc.Input('', className='input', id='email', type='email'),
    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='pw', type='password'),
    
    #Button to toggle between login and signup
    html.Div(
        dcc.Link(
            "Already have an account?", 
            href='/login'
        ), 
        id='signin-toggle', 
        className='login-signup-toggle'
    ),

    #Submit button
    html.Button('Continue', id='submit-val', className='loginbutton'),
]

# Layout: Signup page
signuppage = html.Div(id='layout', className='layout', children=[
        #Entire signup form, with logo, inputs, and buttons
        html.Div(children=signupform, id='form-area', className='form-area'),

        #Empty Div for logic reasons
        html.Div(id='hidden-signin-div')
    ]
)
@dash.callback(
    Output('hidden-signin-div', 'children'),
    Input('submit-val', 'n_clicks'),
    State('user', 'value'),
    State('email', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def register(_, username, email, password):
    # Guard against empty inputs
    if username == '' and email == '' and password == '':
        return html.Div('Enter a username, email, and password')
    elif username == '' and password == '':
        return html.Div('Enter a username and password')
    elif email == '' and password == '':
        return html.Div('Enter a email and password')
    elif username == '' and email == '':
        return html.Div('Enter a username and email')
    elif username == '':
        return html.Div('Username is empty, try again')
    elif email == '':
        return html.Div('Email is empty, try again')
    elif password == '':
        return html.Div('Password is empty, try again')

    auth_response = None
    try:
        auth_response = util.signupRequest(username, email, password)
    except:
        return html.Div('An error occurred while running the useradd script')

    response = auth_response['returnCode']

    if response == '1':
        return dcc.Location(pathname='/login', id='redirect')
    elif response == "2":
        return html.Div(auth_response['message'], style={'color': 'red'})
    else:
        return html.Div('Unhandled error', style={'color': 'red'})

def layout():
    return signuppage