#!/bin/bash

# Set the primary and secondary IP address pairs for prod and qa environments
PROD_FRONTEND_PRIMARY_IP="192.168.191.20"
PROD_FRONTEND_SECONDARY_IP="192.168.191.25"
PROD_BACKEND_PRIMARY_IP="192.168.191.21"
PROD_BACKEND_SECONDARY_IP="192.168.191.26"
PROD_DMZ_PRIMARY_IP="192.168.191.22"
PROD_DMZ_SECONDARY_IP="192.168.191.27"

QA_FRONTEND_PRIMARY_IP="192.168.191.30"
QA_BACKEND_PRIMARY_IP="192.168.191.31"
QA_DMZ_PRIMARY_IP="192.168.191.32"

# Check the environment specified in the argument
case "$1" in
  "prod")
    IP_ADDRESSES=("$PROD_FRONTEND_PRIMARY_IP" "$PROD_BACKEND_PRIMARY_IP" "$PROD_DMZ_PRIMARY_IP" "$PROD_FRONTEND_SECONDARY_IP" "$PROD_BACKEND_SECONDARY_IP" "$PROD_DMZ_SECONDARY_IP")
    MESSAGE="Uploading file to"
    ;;
  "qa")
    IP_ADDRESSES=("$QA_FRONTEND_PRIMARY_IP" "$QA_BACKEND_PRIMARY_IP" "$QA_DMZ_PRIMARY_IP")
    MESSAGE="Uploading file to"
    ;;
  *)
    echo "Error: invalid environment. Please specify 'prod' or 'qa'."
    exit 1
esac

# Set the name of the file to be uploaded
FILE_NAME="$2"

# Use SFTP to upload the file to the IP addresses in the IP_ADDRESSES array
for IP in "${IP_ADDRESSES[@]}"; do
  echo "$MESSAGE $IP..."
  scp "$FILE_NAME" "chris@$IP:~"
done

echo "File successfully uploaded to all machines in $1."
