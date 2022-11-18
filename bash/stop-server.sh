#!/usr/bin/env bash

# Will print error if no process is using port 8050
pid=sudo ss -lptn 'sport = :8050' | grep -oP '(?<=pid=).*?(?=,fd)' | head -n 1 | xargs kill

# Clean all rabbitmq channels
rabbitmqadmin -f tsv -q list connections name > rabbitmq_channels.out
while read -r name; do rabbitmqadmin -q close connection name="${name}"; done < rabbitmq_channels.out
rm rabbitmq_channels.out
echo "Rabbit channels cleared."

# Clear logs file
> ../logging/logs/report.out
echo "Log file cleared."