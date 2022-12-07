#!these are the commands that I used to enable on boot and check to make sure

sudo update-rc.d apache2 enable

sudo update-rc.d mysql enable

sudo update-rc.d rabbitmq-server enable

sudo systemctl list-unit-files --type=service --state=enabled --all

#! instructions on how to make start-server.sh run on boot 
sudo nano /etc/systemd/system/start-server.service

[Unit]
Description=Start Services 

[Service] 
ExecStart= location of .sh file 

[Install] 
WantedBy=multi-user.target

systemctl status start-server.service

sudo systemctl enable start-server.service