#!/bin/bash

# Check the status of the frontend service
frontend_status=$(systemctl is-active frontend.service)

# If the frontend service is active, restart it
if [ "$frontend_status" == "active" ]; then
  systemctl restart frontend.service
  frontend_status=$(systemctl is-active frontend.service)
  if [ "$frontend_status" != "active" ]; then
    echo "Error: failed to restart frontend.service"
  fi
fi

# Check the status of the backend service
backend_status=$(systemctl is-active backend.service)

# If the backend service is active, restart it
if [ "$backend_status" == "active" ]; then
  systemctl restart backend.service
  backend_status=$(systemctl is-active backend.service)
  if [ "$backend_status" != "active" ]; then
    echo "Error: failed to restart backend.service"
  fi
fi

# Check the status of the dmz service
dmz_status=$(systemctl is-active dmz.service)

# If the dmz service is active, restart it
if [ "$dmz_status" == "active" ]; then
  systemctl restart dmz.service
  dmz_status=$(systemctl is-active dmz.service)
  if [ "$dmz_status" != "active" ]; then
    echo "Error: failed to restart dmz.service"
  fi
fi