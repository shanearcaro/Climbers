#!/usr/bin/env bash

# Clean all rabbitmq channels
rabbitmqadmin -f tsv -q list connections name > rabbitmq_channels.out
while read -r name; do rabbitmqadmin -q close connection name="${name}"; done < rabbitmq_channels.out
rm rabbitmq_channels.out

./clear-logs.sh