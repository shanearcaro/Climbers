#!/usr/bin/env bash
# This script is used to run the database services
cd ../logging/
./logListen.php &
cd ../sql/
./loginServer.php &
./statsServer.php 