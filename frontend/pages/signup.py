import dash
from dash import html, dcc, callback, Input, Output, State, no_update
import sys, os

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
from util import *

#Dash requires pages to be registered
dash.register_page(
    __name__, 
    title='Sign Up', 
    path='/signup'
)

# Layout: Signup Form
signupform = [
    #Fun image
    html.Img(src=format_img('logo.png'), 
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
    html.Button('Continue', id='submit', className='loginbutton'),
]

# Layout: Signup page
signuppage = html.Div(id='layout', className='layout', children=[
        #Entire signup form, with logo, inputs, and buttons
        html.Div(children=signupform, id='form-area', className='form-area'),

        #Empty Div for logic reasons
        html.Div(id='hidden-signin-div', style={'display': 'none'})
    ]
)

@dash.callback(
    Output('hidden-signin-div', 'children'),
    Input('submit', 'n_clicks'),
    State('user', 'value'),
    State('email', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def register(_, username, email, password):
    # Guard against empty inputs
    if (username == ''):
        return html.Div('Username is empty, try again')
    elif (email == ''):
        return html.Div('Email is empty, try again')
    elif (password == ''):
        return html.Div('Password is empty, try again')

    # Try to run the useradd script, and return the result
    # We do a try-except block because the script may
    # throw some error and we want to be able to handle that
    # without breaking the webpage
    try:
        add_response = int(run_php_script('userAddRequest.php',
                                            [username, email, password]))
    except:
        return html.Div('An error occurred while running the useradd script')
   
    # Return the response in HTML
    if add_response == 1:
        return dcc.Location(pathname='/logSucc', id='redirect')
    if add_response == 2:
        return html.Div('Invalid login, try again',
                         style={'color': 'red'})
    else:
        return html.Div('Unhandled error',
                         style={'color': 'red'})

def layout():
    return signuppage