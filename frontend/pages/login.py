import dash
from dash import html, dcc, callback, Input, Output, State, no_update
import sys, os

sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
from util import *

dash.register_page(
    __name__,
    title='Login',
    path='/login'
)

# Layout: Login Form
loginform = [
    html.Img(src=format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),
    html.Div('Username', className='label', id='username-label'),
    dcc.Input('', className='input', id='user'),
    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='pw', type='password'),
    html.Div(
        dcc.Link(
            "Already have an account?", 
            href='/signup'
        ), 
        id='login-toggle', 
        className='login-signup-toggle'
    ),
    html.Button('Continue', id='submit', className='loginbutton'),
]

# Layout: Login page
loginpage = html.Div([
    #Login form (with logo)
    html.Div(children=loginform, id='form-area', className='form-area'),

    #Console shit
    html.Div([
        html.Div(
            html.Div('Console', className='consoletitle'), 
                        className='consoletitlecontainer'),
        html.Div('Welcome!', id='result', 
                    className='consoleoutput'),
    ], className='console'),
], id='layout', className='layout')

# Spinner element for loading (WIP)
# TODO: Spinner for login page to show when app is checking credentials
spinner = html.Div([html.Div(), html.Div(), html.Div(), html.Div()], 
                   className='lds-ellipsis')

# Layout: Success (Temporary)
success = html.Div('Success')

# Tacki smells like spaghetti, which is a good thing
#(copilot wrote the spaghetti part lmao) 

@dash.callback(
    Output('result', 'children'),
    Output('layout', 'children'),
    Input('submit', 'n_clicks'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def authenticate(_, username, password):
    # Guard against empty inputs
    if ((username == '') and (password == '')):
        return html.Div('Enter a username and password'), no_update
    elif (username == ''):
        return html.Div('Username is empty, try again'), no_update
    elif (password == ''):
        return html.Div('Password is empty, try again'), no_update

    try:
        auth_response = int(run_php_script('loginRequest.php',
                                            [username, password]))
    except:
        return html.Div('An error occurred while running the login script'),no_update
   
    # Return the response in HTML
    if auth_response == 1:
        return no_update, success
    if auth_response == 2:
        return html.Div('Invalid login, try again',
                         style={'color': 'red'}), no_update
    else:
        return html.Div('Unhandled error',
                         style={'color': 'red'}), no_update
                         
def layout():
    return loginpage