#!/bin/bash

# Store the current directory in a variable
app_dir="/home/chris/it490"

remoteip="192.168.191.60"

# Prompt the user for the desired version number and cluster name, 
# as well as the username and password for the sftp command
read -p "Enter the desired version number: " version
read -p "Enter the desired cluster name: " cluster
read -p "Enter the sftp username: " sftp_user
#read -s -p "Enter the sftp password: " sftp_password

# Create the tarball filename using the inputted version number and cluster name
tarball_name="$cluster-$version.tar.gz"

# Tar the current directory using the generated filename
tar -czvf "$tarball_name" "$app_dir"

# Transfer the tarball to the remote server using scp
scp "$tarball_name" "$sftp_user@$remoteip:~/Deployment/"

# Disconnect from the remote server
exit

#echo "connecting to $remoteip..."
#sftp "$sftp_user@$remoteip"  << EOF
#put $tarball_name
#EOF

# Connect to the remote IP using sftp and the provided username and password
#sftp "$sftp_user:$sftp_password@$remoteip" << EOF

# Navigate to the desired directory on the remote server
#cd ~/Deployment/

# Transfer the tarball to the remote server
#put "$tarball_name"

# Disconnect from the remote server
#exit
#EOF