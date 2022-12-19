#!/bin/bash

# Check the status of the frontend service
frontend_status=$(systemctl is-active frontend.service)

# If the frontend service is active, start it
if [ "$frontend_status" != "active" && "$frontend_status" != "unknown"]; then
  systemctl start frontend.service
  frontend_status=$(systemctl is-active frontend.service)
  if [ "$frontend_status" != "active" ]; then
    echo "Error: failed to start frontend.service"
  fi
fi

# Check the status of the backend service
backend_status=$(systemctl is-active backend.service)

# If the backend service is active, start it
if [ "$backend_status" != "active" && "$backend_status" != "unknown"]; then
  systemctl start backend.service
  backend_status=$(systemctl is-active backend.service)
  if [ "$backend_status" != "active" ]; then
    echo "Error: failed to start backend.service"
  fi
fi

# Check the status of the dmz service
dmz_status=$(systemctl is-active dmz.service)

# If the dmz service is active, stop it
if [ "$dmz_status" != "active" && "$dmz_status" != "unknown"]; then
  systemctl start dmz.service
  dmz_status=$(systemctl is-active dmz.service)
  if [ "$dmz_status" != "active" ]; then
    echo "Error: failed to start dmz.service"
  fi
fi