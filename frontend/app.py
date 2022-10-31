import sys, os
import dash
from dash import Dash, html

# Initialize Dash app
app = Dash(__name__, 
          update_title='', 
          suppress_callback_exceptions=True,
          use_pages=True
        )

app.layout = html.Div(
    dash.page_container
)

if __name__ == "__main__":
    app.run_server(debug=True)


# Initial app layout
# - The login page will always be first
# - TODO: Cookies/Session?
# app.layout = login