#!/bin/bash

# Set the name of the directory to extract the tar file to
EXTRACT_DIRECTORY_NAME="it490"

#Move outside of the it490 project directory
cd ../../

# Run the loop indefinitely
while true
do
  # Check if a tar.gz file exists in the current directory
  if ls *.tar.gz &> /dev/null
  then
    # Extract the tar file to the specified directory
    tar -xf *.tar.gz -C "$EXTRACT_DIRECTORY_NAME"

    # Delete the tar file
    rm *.tar.gz

    # Run the restart_service.sh script
    ./it490/bash/restart_service.sh
  fi

  # Sleep for 10 seconds before checking for the tar file again
  sleep 10
done
