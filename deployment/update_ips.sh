#!/bin/bash

# Set the primary and secondary IP address pairs
FRONTEND_PRIMARY_IP="192.168.191.20"
FRONTEND_SECONDARY_IP="192.168.191.25"

BACKEND_PRIMARY_IP="192.168.191.21"
BACKEND_SECONDARY_IP="192.168.191.26"

DMZ_PRIMARY_IP="192.168.191.22"
DMZ_SECONDARY_IP="192.168.191.27"

# Set the name of the file to be read
FILE_NAME="../config/config.ini"

# Check which IP pair is specified in the argument
case "$1" in
  "frontend")
    PRIMARY_IP="$FRONTEND_PRIMARY_IP"
    SECONDARY_IP="$FRONTEND_SECONDARY_IP"
    ;;
  "backend")
    PRIMARY_IP="$BACKEND_PRIMARY_IP"
    SECONDARY_IP="$BACKEND_SECONDARY_IP"
    ;;
  "dmz")
    PRIMARY_IP="$DMZ_PRIMARY_IP"
    SECONDARY_IP="$DMZ_SECONDARY_IP"
    ;;
  *)
    echo "Error: invalid argument. Please specify which IP pair to update (frontend, backend, or dmz)."
    exit 1
esac

# Get the name of the tar file in the current directory
TAR_FILE_NAME=$(ls *.tar.gz)

# Unpack the tar file
tar -xzf "$TAR_FILE_NAME"

# Search the config file for both IPs in the pair
PRIMARY_IP_FOUND=$(grep "$PRIMARY_IP" $FILE_NAME)
SECONDARY_IP_FOUND=$(grep "$SECONDARY_IP" $FILE_NAME)

# Replace the found IP with the other IP in the config file
if [ -n "$PRIMARY_IP_FOUND" ]
then
  sed -i "s/$PRIMARY_IP/$SECONDARY_IP/g" "$FILE_NAME"
else
  sed -i "s/$SECONDARY_IP/$PRIMARY_IP/g" "$FILE_NAME"
fi

# Repack the tar file with the original name
tar -czf "$TAR_FILE_NAME"

# Run the SFTP script to upload the tar file to the prod environment
./push_to_cluster.sh "prod" "$TAR_FILE_NAME"
