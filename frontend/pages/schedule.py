import dash
from dash import html, dcc, callback, Input, Output, State, no_update
import sys, os
import plotly.express as px

sys.path.append(os.path.join(os.path.dirname(__file__), '..'))
from util import *

dash.register_page(
    __name__,
    title='Schedule',
    path='/schedule'
)

scheduling = html.Div([
    'hi'
])

def layout():
    return scheduling
