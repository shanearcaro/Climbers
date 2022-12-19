#!/bin/bash

# Set the name of the directory to extract the tar file to
EXTRACT_DIRECTORY_NAME="it490"

# Run the loop indefinitely
while true
do
  # Check if a tar.gz file exists in the current directory
  if [-f "*.tar.gz"]
  then
    # Move into the bash project directory
    cd it490/bash/

    #Stop any related services
    ./stop_service.sh

    cd ../../

    # Extract the tar file to the specified directory
    tar -xf --delete *.tar.gz -C "$EXTRACT_DIRECTORY_NAME"

    # Delete the tar file
    rm *.tar.gz

    # Move into the bash project directory
    cd it490/bash/

    # Run the restart_service.sh script
    ./start_service.sh

    cd ../../
  fi

  # Sleep for 10 seconds before checking for the tar file again
  sleep 10
done
