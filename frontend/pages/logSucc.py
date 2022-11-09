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

@dash.callback(
    Output('userid-text', 'children'),
    [Input('session-userid', 'data')])

def on_data(data):
    id = data
    return html.Div("User ID: " + id)

def layout():
    return success