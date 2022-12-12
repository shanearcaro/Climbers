import dash
from dash import Dash, html, dcc

#from pages import login, signup

# Initialize Dash app
app = Dash(__name__,
                update_title='', 
                suppress_callback_exceptions=True,
                use_pages=True,
)

app.layout = html.Div(children=[
        dash.page_container, 
        dcc.Store(id='session-userid', storage_type='session')
    ]
)

# Self signed certs
context = ("../certificate/cert.pem", "../certificate/key.pem")

if __name__ == "__main__":
    app.run(debug=True, ssl_context=context)


# Initial app layout
# - The login page will always be first
# - TODO: Cookies/Session?
# app.layout = login