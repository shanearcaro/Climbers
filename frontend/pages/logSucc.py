import dash
from dash import Input, Output, State, callback, dcc, html, no_update

dash.register_page(
    __name__, 
    title='Success!', 
    path='/logSucc'
)

# Layout: Success (Temporary)
success = html.Div( children=[
    html.Div('Success!'),
    html.Div(id='userid-text'),
    dcc.Interval(
            id='interval',
            interval=1*1000, # in milliseconds
            n_intervals=0
        )
])

@callback(
    Output('userid-text', 'children'),
    Input('interval', 'modified_timestamp'),
    State('session', 'data'),
)
def getuid(n, data):
    data = data or {}
    return data.get('userid', 'No User ID')


def layout():
    return success