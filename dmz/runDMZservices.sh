#!/usr/bin/env bash
# This script is used to run the DMZ services
cd ../logging/
./logListen.php &
cd ../dmz/
./apiServer.php