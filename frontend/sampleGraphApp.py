from gatherAreas import get_raw_data
from dash import Dash, html, dcc
import pandas as pd
import plotly.figure_factory as ff 

app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Get raw data for graph (formatted raw dictionaries)
raw_data = get_raw_data()

# Get pandas dataframe for figure
df = pd.DataFrame(raw_data)

# Create figure object for hex graph
fig = ff.create_hexbin_mapbox(
    data_frame=df, 
    lat="lat", lon="lng",
    height=800,
    nx_hexagon=15, opacity=0.5, # 15 h hexagons 
    labels={"color": "Climb Count"}, # Color by climb
    min_count=1, # Does not count 0 climbs
    color_continuous_scale="Viridis",
    show_original_data=True, # Show points underneath
    original_data_marker=dict(
                    size=10, opacity=0.6, 
                    color="lime")
)
# Need a map underneath
fig.update_layout(mapbox_style="open-street-map")

# App layout
app.layout = html.Div([
    dcc.Graph(figure=fig, style={
        'height':'100%'
    })
], style={
    'width': '90%',
    'display': 'block',
    'margin': 'auto'
})

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)