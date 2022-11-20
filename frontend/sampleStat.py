import sys, os
import dash
from dash import Dash, html, dcc, callback, Input, Output, State, no_update

#Relative path import for util.py
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
import util

app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

username = 'chi'

app.layout = html.Div([
#----------------------------------------------------Begin User Card
    html.Div(
        id=f'{username}-user-card',
        className='user-card',
        children=[
            
            #Left side of user card with personal info
            html.Div(
                id=f'{username}-user-card-left',
                className='user-card-left',
                children=[
                html.Img(src=util.format_img('usericon.png'), 
                        className='user-icon',),
                html.H2(f"{username}", className='user-name'),
                html.Button(['Chat'], id=f'{username}-chat-button',
                            className='chat-button'),
                ],
            ),

            #Right side of user card with stats
            html.Div(
                id=f'{username}-user-card-right',
                className='user-card-right',
                children=[
                    html.P('Total Ascents:'),
                    html.P('Highest Grade:'),
                    html.P('Most Recent Ascent:'),
                ],
            ),

            #Block button
            html.Button(
                children=[],
                id=f'{username}-blockbtn',
                className='block-button'
            ),
        ],
    )
#------------------------------------------------------End User Card
])

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)