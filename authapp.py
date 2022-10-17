import base64
from dash import Dash, html, dcc, Input, Output, State
import mysql.connector

app = Dash(__name__, update_title=None, suppress_callback_exceptions=True)

logins = [['admin', '0']]

# Dash requires a special image format
def format_img(img):
    b64encoded_img=base64.b64encode(open(f'assets/{img}', 'rb').read())
    return f'data:image/png;base64,{b64encoded_img.decode()}'

def connectDB():
    config = {
        'user' : 'root',
        'password' : '',
        'host' : '192.168.191.63',
        'database' : 'IT490'
    }
    db = mysql.connector.connect(**config)
    return db

app.layout = html.Div([
    html.Div([
        html.Img(src=format_img('logo.png'), style={'margin': '30px auto', 'display': 'block'}),
        html.Div('Username', className='label'),
        dcc.Input('', className='input', id='user'),
        html.Div('Password', className='label'),
        dcc.Input('', className='input', id='pw'),
        html.Button('Continue', id='submit', className='loginbutton'),
        html.Div([
            html.Div(html.Div('Console', className='consoletitle'), className='consoletitlecontainer'),
            html.Div('sample', id='result', className='consoleoutput'),
        ], className='console')
    ],className='login-area')
], className='layout')

@app.callback(
    Output('result', 'children'),
    Input('submit', 'n_clicks'),
    Input('user', 'n_submit'),
    Input('pw', 'n_submit'),
    State('user', 'value'),
    State('pw', 'value'),
    prevent_initial_call=True
)
def submit(submit, usersubmit, pwsubmit, user, pw):
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

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)