import base64
from dash import Dash, html, dcc, Input, Output, State
import subprocess

app = Dash(__name__, update_title=None, suppress_callback_exceptions=True)

logins = [['admin', '0']]

# Dash requires a special image format
def format_img(img):
    b64encoded_img=base64.b64encode(open(f'assets/{img}', 'rb').read())
    return f'data:image/png;base64,{b64encoded_img.decode()}'

app.layout = html.Div([
    html.Div([
        html.Img(src=format_img('logo.png'), style={'margin': '30px auto', 'display': 'block'}),
        html.Div('Username', className='label', id='hi'),
        dcc.Input('', className='input', id='user'),
        html.Div('Password', className='label'),
        dcc.Input('', className='input', id='pw', type='password'),
        html.Button('Continue', id='submit', className='loginbutton'),
        html.Div([
            html.Div(html.Div('Console', className='consoletitle'), className='consoletitlecontainer'),
            html.Div('Welcome!', id='result', className='consoleoutput'),
        ], className='console')
    ],className='login-area')
], className='layout')

# Tacki smells
@app.callback(
    Output('hi', 'children'),
    Input('user', 'n_submit'),
    Input('pw', 'n_submit'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def submit(usersubmit, pwsubmit, user, pw):
    if ((user == '') and (pw == '')):
        return 'Enter a username and password'
    elif (user == ''):
        return 'Username is empty, try again'
    elif (pw == ''):
        return 'Password is empty, try again'
    for login in logins:
        if (user.lower() == login[0] and pw.lower() == login[1]):
            return f'Success! Welcome, {user}'
        else:
            return 'Invalid login, try again'
    return 'Unhandled error'

@app.callback(
    Output('result', 'children'),
    Input('submit', 'n_clicks'),
    prevent_initial_call=True
)
def rabbit(submit):
                            #Right here, I hard coded the crudentials and this works
                            #So the crudentials can be passed in from the login page like this
                            #chisux - username      abc - password/hash
    proc = subprocess.Popen("php rabbitConnector.php chisux abc", shell=True, stdout=subprocess.PIPE)
    response = proc.stdout.read()
    #This decode is what got the yo example working
    #response = response.decode('utf-8')
    return response

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)
