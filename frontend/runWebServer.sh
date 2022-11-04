#!/usr/bin/env bash
# This script is used to run the web server
cd ../logging/
./logListen.php &
cd ../frontend/
python3 app.py