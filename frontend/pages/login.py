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
    #Empty Div for logic reasons
    html.Div(id='hidden-login-div', style={
        'color':'red',
        'padding-bottom':'10px'
    }),

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
    html.Button('Continue', id='submit-val', className='loginbutton'),
]

# Layout: Login page
loginpage = html.Div(id='layout', className='layout', children=[
    #Entire signup form, with logo, inputs, and buttons
    html.Div(children=loginform, id='form-area', className='form-area'),
])

# Spinner element for loading (WIP)
# TODO: Spinner for login page to show when app is checking credentials
spinner = html.Div([html.Div(), html.Div(), html.Div(), html.Div()], 
                   className='lds-ellipsis')

# Tacki smells like spaghetti, which is a good thing
#(copilot wrote the spaghetti part lmao) 

@dash.callback(
    [Output('hidden-login-div', 'children'), 
    Output('session-userid', 'data'),],
    Input('submit-val', 'n_clicks'),
    Input('user', 'n_submit'),
    Input('pw', 'n_submit'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def authenticate(_, _user, _pw, username, password):
    # Guard against empty inputs

    if username == '' and password == '':
        return html.Div('Enter a username and password'), -1
    elif username == '':
        return html.Div('Username is empty, try again'), -1
    elif password == '':
        return html.Div('Password is empty, try again'), -1

    auth_response = None
    try:
        auth_response = util.loginRequest(username, password)
    except:
        return html.Div('An error occurred while running the login script')
   
    # Return the response in HTML
    if auth_response.get("returnCode") == "1":
        dcc.Store(id='stored-userid', 
                data=auth_response.get("userid"), 
                storage_type='session')
        return dcc.Location(pathname='/logSucc', id='redirect')
    if auth_response.get("returnCode") == "2":
        return html.Div('Invalid login, try again',
                         style={'color': 'red'})
    else:
        return html.Div('Unhandled error', style={'color': 'red'}), -1

def layout():
    return loginpage