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
    title='Login',
    path='/login'
)

# Layout: Login Form
loginform = [
    #Fun image
    html.Img(src=util.format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),

    #Actual form area
    html.Div('Username', className='label', id='username-label'),
    dcc.Input('', className='input', id='user'),
    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='pw', type='password'),

    #Button to toggle between login and signup
    html.Div(
        dcc.Link(
            "Don't Have an Account?", 
            href='/signup'
        ), 
        id='login-toggle', 
        className='login-signup-toggle'
    ),

    #Submit button
    html.Button('Continue', id='submit', className='loginbutton'),
]

# Layout: Login page
loginpage = html.Div(id='layout', className='layout', children=[
    #Entire signup form, with logo, inputs, and buttons
    html.Div(children=loginform, id='form-area', className='form-area'),

    #Empty Div for logic reasons
    html.Div(id='hidden-login-div', style={'display': 'none'})
])

# Spinner element for loading (WIP)
# TODO: Spinner for login page to show when app is checking credentials
spinner = html.Div([html.Div(), html.Div(), html.Div(), html.Div()], 
                   className='lds-ellipsis')

# Tacki smells like spaghetti, which is a good thing
#(copilot wrote the spaghetti part lmao) 

@dash.callback(
    Output('hidden-login-div', 'children'),
    Input('submit', 'n_clicks'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def authenticate(_, username, password):
    # Guard against empty inputs
    if ((username == '') and (password == '')):
        return html.Div('Enter a username and password')
    elif (username == ''):
        return html.Div('Username is empty, try again')
    elif (password == ''):
        return html.Div('Password is empty, try again')

    # Try to run the auth script, and return the result
    # We do a try-except block because the script may
    # throw some error and we want to be able to handle that
    # without breaking the webpage
    auth_response = None
    try:
        auth_response = util.loginRequest(username, password)
    except:
        return html.Div('An error occurred while running the login script')
   
    # Return the response in HTML
    if auth_response["returnCode"] == "1":
        dcc.Store(id='userid', 
                data=auth_response["userid"], 
                storage_type='session')
        return dcc.Location(pathname='/logSucc', id='redirect')
    if auth_response["returnCode"] == "2":
        return html.Div('Invalid login, try again',
                         style={'color': 'red'})
    else:
        return html.Div('Unhandled error',
                         style={'color': 'red'})
                         
def layout():
    return loginpage