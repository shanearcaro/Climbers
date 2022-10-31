import base64, subprocess
from dash import Dash, html, dcc, Input, Output, State, no_update

# Initialize Dash app
app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Dash requires a special image format
def format_img(img):
    b64encoded_img=base64.b64encode(open(f'assets/{img}', 'rb').read())
    return f'data:image/png;base64,{b64encoded_img.decode()}'

def run_php_script(path, args):
    # Run a PHP script and return the output

    # Set up the command, and arguments if it has any
    shellstr = f"php {path}"
    if args:
        for arg in args:
            shellstr += f" {arg}"

    # Run the script
    proc = subprocess.Popen(
        shellstr, shell=True, stdout=subprocess.PIPE)

    # Get output from php script
    response = proc.stdout.read()

    # Decode bytes to string, return raw string
    return response.decode('utf-8')

# Spinner element for loading (WIP)
# TODO: Spinner for login page to show when app is checking credentials
spinner = html.Div([html.Div(), html.Div(), html.Div(), html.Div()], 
                   className='lds-ellipsis')

# Layout: Success (Temporary)
success = html.Div('Success')

# Layout: Login page
login = html.Div([
    html.Div([
        #Login form (with logo)
        html.Img(src=format_img('logo.png'), 
                 style={'margin': '30px auto', 'display': 'block'}),
        html.Div('Username', className='label', id='username-label'),
        dcc.Input('', className='input', id='user'),
        html.Div('Password', className='label'),
        dcc.Input('', className='input', id='pw', type='password'),
        html.Button("Don't have an account?", id='toggle', 
                    className='login-signup-button'),
        html.Button('Continue', id='submit', className='loginbutton'),

        #Console shit
        html.Div([
            html.Div(
                html.Div('Console', className='consoletitle'), 
                         className='consoletitlecontainer'),
            html.Div('Welcome!', id='result', 
                     className='consoleoutput'),
        ], className='console')
    ],className='login-area')
], id='layout', className='layout')

#Layout: Sign up page
signup = html.Div([
    html.Div([
        #Sign up form (with logo)
        html.Img(src=format_img('logo.png'), 
                 style={'margin': '30px auto', 'display': 'block'}),
        html.Div('Username', className='label'),
        dcc.Input('', className='input', id='user'),
        html.Div('E-Mail', className='label'),
        dcc.Input('', className='input', id='email', type='email'),
        html.Div('Password', className='label'),
        dcc.Input('', className='input', id='pw', type='password'),
        html.Button('Already have an account?', id='toggle', 
                    className='login-signup-button'),
        #html.Button('Continue', id='submit', className='loginbutton'),

        #Console shit
        html.Div([
            html.Div(
                html.Div('Console', className='consoletitle'), 
                         className='consoletitlecontainer'),
            html.Div('Welcome!', id='result', 
                     className='consoleoutput'),
        ], className='console')
    ],className='login-area')
], id='layout', className='layout')

# Initial app layout
# - The login page will always be first
# - TODO: Cookies/Session?
app.layout = login

# Tacki smells like spaghetti, which is a good thing
#(copilot wrote the spaghetti part lmao) 

@app.callback(
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

    auth_response = int(run_php_script('loginRequest.php',
                                        [username, password]))
   
    # Return the response in HTML
    if auth_response == 1:
        return no_update, success
    if auth_response == 2:
        return html.Div('Invalid login, try again',
                         style={'color': 'red'}), no_update
    else:
        return html.Div('Unhandled error',
                         style={'color': 'red'}), no_update

@app.callback(
    Input('toggle', 'n_clicks'),
    prevent_initial_call=True
)
def toggle_login_page(_):
    # Toggle between login and sign up
    if app.layout == login:
        return no_update, signup
    else:
        return no_update, login

# Run dash server
# - Set debug=False when in deployment
if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)
