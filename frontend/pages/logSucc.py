import dash
from dash import html, dcc, callback, Input, Output, State, no_update

dash.register_page(
    __name__, 
    title='Success!', 
    path='/logSucc'
)

# Layout: Success (Temporary)
success = html.Div('Success!')

def layout():
    return success