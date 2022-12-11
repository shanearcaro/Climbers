#ensures systems up to date and contains required ssh updates
sudo apt-get update && sudo apt-get install openssh-server

#make sure firewall is first disabled
sudo ufw disable

#Allow for communication across machines through SSH 
sudo ufw allow from 192.168.191.20 to any port 22
sudo ufw allow from 192.168.191.21 to any port 22
sudo ufw allow from 192.168.191.22 to any port 22
sudo ufw allow from 192.168.191.30 to any port 22
sudo ufw allow from 192.168.191.31 to any port 22
sudo ufw allow from 192.168.191.32 to any port 22
sudo ufw allow from 192.168.191.40 to any port 22
sudo ufw allow from 192.168.191.41 to any port 22
sudo ufw allow from 192.168.191.42 to any port 22
sudo ufw allow from 192.168.191.50 to any port 22

#blocks the user from accessing port 80, may prevent apt updates 
sudo ufw deny out 80
sudo ufw deny http 

#allows user to access resources required for project
sudo ufw allow https
sudo ufw allow 443

sudo ufw allow 465
sudo ufw allow 5762

sudo ufw allow 7

sudo ufw allow OpenSSH

#enables the firewall 
sudo ufw enable


 
