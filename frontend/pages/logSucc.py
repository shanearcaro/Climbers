import dash
from dash import Input, Output, State, callback, dcc, html, no_update

dash.register_page(
    __name__, 
    title='Success!', 
    path='/logSucc'
)

# Layout: Success (Temporary)
success = html.Div(children=[
    html.Div('Success!')
    html.Div(id='userid_text')
])

@callback(
    Output('userid-text', 'children'),
    State('session', 'data'),
)
def getuid():
    return
def layout():
    return success