import base64
from dash import Dash, html, dcc, Input, Output, State, no_update
import subprocess

app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Dash requires a special image format
def format_img(img):
    b64encoded_img=base64.b64encode(open(f'assets/{img}', 'rb').read())
    return f'data:image/png;base64,{b64encoded_img.decode()}'

spinner = html.Div([html.Div(), html.Div(), html.Div(), html.Div()], className='lds-ellipsis')

success = html.Div('Success')

login = html.Div([
    html.Div([
        html.Img(src=format_img('logo.png'), 
                 style={'margin': '30px auto', 'display': 'block'}),
        html.Div('Username', className='label', id='hi'),
        dcc.Input('', className='input', id='user'),
        html.Div('Password', className='label'),
        dcc.Input('', className='input', id='pw', type='password'),
        html.Button('Continue', id='submit', className='loginbutton'),
        html.Div([
            html.Div(html.Div('Console', className='consoletitle'), 
                     className='consoletitlecontainer'),
            html.Div('Welcome!', id='result', 
                     className='consoleoutput'),
        ], className='console')
    ],className='login-area')
], id='layout', className='layout')

app.layout = login

# Tacki indeed does smell. >:D

@app.callback(
    Output('result', 'children'),
    Output('layout', 'children'),
    Input('submit', 'n_clicks'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def authenticate(_, username, password):
    #Guard against empty inputs
    if ((username == '') and (password == '')):
        return html.Div('Enter a username and password'), no_update
    elif (username == ''):
        return html.Div('Username is empty, try again'), no_update
    elif (password == ''):
        return html.Div('Password is empty, try again'), no_update

    #Call php auth script with username and password
    proc = subprocess.Popen(
        f"php loginRequest.php {username} {password}", 
        shell=True, stdout=subprocess.PIPE)

    #Get output from php script
    response = proc.stdout.read()

    #This decode is what got the yo example working
    #Cast to int becuase the response is a return code
    response = int(response.decode('utf-8'))
   
    #Return the response in HTML
    if response == 1:
        return no_update, success
    if response == 2:
        return html.Div('Invalid login, try again',
                         style={'color': 'red'}), no_update
    else:
        return html.Div('Unhandled error',
                         style={'color': 'red'}), no_update

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)
