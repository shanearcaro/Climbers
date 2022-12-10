#!/usr/bin/env bash


# Starting server
cd ../servers/

nohup ./login-server.php >> ../logging/logs/report.out &
nohup ./chat-server.php >> ../logging/logs/report.out &

# Start app
cd ../frontend/
nohup python3 app.py >> ../logging/logs/report.out &    