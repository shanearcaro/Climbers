#!these are the commands that I used to enable on boot and check to make sure

sudo update-rc.d apache2 enable

sudo update-rc.d mysql enable

sudo update-rc.d rabbitmq-server enable

sudo systemctl list-unit-files --type=service --state=enabled --all