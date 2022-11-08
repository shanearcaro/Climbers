#!/usr/bin/env bash

# Will print error if no process is using port 8050
pid=sudo ss -lptn 'sport = :8050' | grep -oP '(?<=pid=).*?(?=,fd)' | head -n 1 | xargs kill
eval $pid