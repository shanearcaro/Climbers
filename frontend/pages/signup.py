import dash
from dash import html, dcc, callback, Input, Output, State, no_update
import sys, os

sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
from util import *

dash.register_page(__name__)

# Layout: Signup Form
signupform = [
    html.Img(src=format_img('logo.png'), 
            style={'margin': '30px auto', 'display': 'block'}),
    html.Div('Username', className='label'),
    dcc.Input('', className='input', id='user'),
    html.Div('E-Mail', className='label'),
    dcc.Input('', className='input', id='email', type='email'),
    html.Div('Password', className='label'),
    dcc.Input('', className='input', id='pw', type='password'),
    html.Button('Already have an account?', id='signin-toggle', 
                className='login-signup-toggle'),
    #html.Button('Continue', id='submit', className='loginbutton'),
]

# Layout: Signup page
signuppage = html.Div([
    #Login form (with logo)
    html.Div(children=signupform, id='form-area', className='form-area'),

    #Console shit
    html.Div([
        html.Div(
            html.Div('Console', className='consoletitle'), 
                        className='consoletitlecontainer'),
        html.Div('Welcome!', id='result', 
                    className='consoleoutput'),
    ], className='console'),
    dcc.Store(id='current-form', data='login'),
], id='layout', className='layout')