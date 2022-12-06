#!/usr/bin/env bash

# Start logging server
cd ../logging/
nohup ./logListen.php >> ./logs/report.out &

# # Start login server
# cd ../sql/
# nohup ./loginServer.php >> ../logging/logs/report.out &

# # Start chat server
# nohup ./chatServer.php >> ../logging/logs/report.out &

# # Start dmz server
# cd ../dmz/
# nohup ./apiServer.php >> ../logging/logs/report.out &

# Starting server
cd ../sql/
nohup ./server.php >> ../logging/logs/report.out &

# Start app
cd ../frontend/
python3 app.py