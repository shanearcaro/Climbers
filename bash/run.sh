#!/usr/bin/env bash

# Start logging server
cd ../logging/
nohup ./logListen.php >> ./logs/report.out &

# Start login server
cd ../sql/
nohup ./sqlServer.php >> ../logging/logs/report.out &

# Start chat server
nohup ./chatServer.php >> ../logging/logs/report.out &

# Start dmz server
cd ../dmz/
nohup ./apiServer.php >> ../logging/logs/report.out &

# Start app
cd ../frontend/
nohup python3 app.py >> ../logging/logs/report.out &

# Use this command to get pid and kill it
# sudo ss -lptn 'sport = :8050'
