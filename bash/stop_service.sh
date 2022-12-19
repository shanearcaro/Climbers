#!/bin/bash

# Check the status of the frontend service
frontend_status=$(systemctl is-active frontend.service)

# If the frontend service is active, stop it
if [ "$frontend_status" == "active" ]; then
  systemctl stop frontend.service
  frontend_status=$(systemctl is-active frontend.service)
  if [ "$frontend_status" != "active" ]; then
    echo "Error: failed to stop frontend.service"
  fi
fi

# Check the status of the backend service
backend_status=$(systemctl is-active backend.service)

# If the backend service is active, stop it
if [ "$backend_status" == "active" ]; then
  systemctl stop backend.service
  backend_status=$(systemctl is-active backend.service)
  if [ "$backend_status" != "active" ]; then
    echo "Error: failed to stop backend.service"
  fi
fi

# Check the status of the dmz service
dmz_status=$(systemctl is-active dmz.service)

# If the dmz service is active, stop it
if [ "$dmz_status" == "active" ]; then
  systemctl stop dmz.service
  dmz_status=$(systemctl is-active dmz.service)
  if [ "$dmz_status" != "active" ]; then
    echo "Error: failed to stop dmz.service"
  fi
fi