#!/bin/bash

# Set the IP address pairs
frontend_ip1=192.168.191.20
frontend_ip2=192.168.191.25

backend_ip1=192.168.191.21
backend_ip2=192.168.191.26

dmz_ip1=192.168.191.22
dmz_ip2=192.168.191.27

while true; do
  # Ping the first IP in each pair
  if ! ping -c 1 $frontend_ip1 &> /dev/null; then
    # If the ping fails, switch to the second IP in the pair and run the script
    temp_ip=$frontend_ip1
    frontend_ip1=$frontend_ip2
    frontend_ip2=$temp_ip
    ./update_ips.sh frontend
  fi

  if ! ping -c 1 $backend_ip1 &> /dev/null; then
    # If the ping fails, switch to the second IP in the pair and run the script
    temp_ip=$backend_ip1
    backend_ip1=$backend_ip2
    backend_ip2=$temp_ip
    ./update_ips.sh backend
  fi

  if ! ping -c 1 $dmz_ip1 &> /dev/null; then
    # If the ping fails, switch to the second IP in the pair and run the script
    temp_ip=$dmz_ip1
    dmz_ip1=$dmz_ip2
    dmz_ip2=$temp_ip
    ./update_ips.sh dmz
  fi

  # Sleep for a minute before pinging again
  sleep 60
done
