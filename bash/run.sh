#!/usr/bin/env bash

# Start logging server
cd ../logging/
nohup ./logListen.php >> ./logs/logging_report.out &

# Start database server
cd ../sql/
nohup ./sqlServer.php >> ../logging/logs/sql_report.out &

# Start dmz server
cd ../dmz/
nohup ./apiServer.php >> ../logging/logs/dmz_report.out &

# Start app
cd ../frontend/
nohup python3 app.py >> ../logging/logs/frontend_report.out &

# Use this command to get pid and kill it
# sudo ss -lptn 'sport = :8050'
