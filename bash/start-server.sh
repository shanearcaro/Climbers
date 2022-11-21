#!/usr/bin/env bash

# Start logging server
cd ../logging/
nohup ./logListen.php >> ./logs/report.out &

# Start login server
cd ../sql/
nohup ./loginServer.php >> ../logging/logs/report.out &

# Start chat server
nohup ./chatServer.php >> ../logging/logs/report.out &

# Start dmz server
cd ../dmz/
nohup ./apiServer.php >> ../logging/logs/report.out &

# Start app
cd ../frontend/
nohup python3 app.py >> ../logging/logs/report.out &