import dash
from dash import Dash, Input, Output, html, dcc

dash.register_page(
    __name__,  
    path='/'
)

home = html.Div(id='location')

dash.callback(
    Output('location', 'children'),
    Input('session-userid', 'data')
)
def home(userid):
    if userid > 0:
        return dcc.Location(pathname='/social', id='redirect')
    else:
        return dcc.Location(pathname='/login', id='redirect')
    
def layout():
    return home
    

