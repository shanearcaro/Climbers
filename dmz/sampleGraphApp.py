from gatherAreas import get_raw_data
from dash import Dash, html, dcc
import pandas as pd
import plotly.figure_factory as ff 

app = Dash(__name__, update_title='', suppress_callback_exceptions=True)

# Get raw data for graph (formatted raw dictionaries)
raw_data = get_raw_data()

# Get pandas dataframe for figure
df = pd.DataFrame(raw_data)

fig = ff.create_hexbin_mapbox(
    data_frame=df, 
    lat="lat", lon="lng",
    nx_hexagon=15, opacity=0.5, 
    labels={"color": "Climb Count"},
    min_count=1,
    color_continuous_scale="Viridis",
    show_original_data=True,
    original_data_marker=dict(
                    size=10, opacity=0.6, 
                    color="lime")
)
fig.update_layout(mapbox_style="open-street-map")

app.layout = html.Div([
    dcc.Graph(figure=fig)
], style={
    'width': '90%',
    'display': 'block',
    'margin': 'auto'
})

if __name__ == '__main__':
    app.run_server(host="0.0.0.0", port="8050", debug=True)