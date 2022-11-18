import dash
from dash import Dash, html, dcc
from dash_extensions.enrich import DashProxy, MultiplexerTransform

#from pages import login, signup

# Initialize Dash app
app = DashProxy(__name__,
                update_title='', 
                suppress_callback_exceptions=True,
                use_pages=True,
                transforms=[MultiplexerTransform()]
)

app.layout = html.Div(children=[
    dash.page_container, 
    dcc.Store(id='session-userid', storage_type='session')
    ]
)

# This was provided by the Dash documentation
#
# @callback(Output('page-content', 'children'),
#               Input('url', 'pathname'))
# def display_page(pathname):
#     if pathname == '/login':
#         return login.layout
#     elif pathname == '/signup':
#         return signup.layout
#     else:
#         return '404'

if __name__ == "__main__":
    app.run_server()


# Initial app layout
# - The login page will always be first
# - TODO: Cookies/Session?
# app.layout = login