## Services

In this folder are the service unit files that define the parts of 
this application as systemd services

For purposes of the project they are stored here, however, in order to
function properly there is some configuration required.

#### Configuration and Installation

In each of the files, the ```ExecStart``` variable must be set to
point to the relavant script.

For example, the ```frontend.service``` file should have its ```ExecStart```
variable set to point to ```runWebServer.sh``` script on the frontend machine.

These files also must be moved into ```/etc/systemd/system/``` 
and enabled through ```systemctl``` as such:

``` systemctl enable <service-name> ```
``` systemctl start <service-name> ```

Where ```<service-name>``` is the name of the service, in this case 
either ```backend```, ```frontend```, or ```dmz```.

The status of a running service can be viewed with:

``` systemctl status <service-name> ```

To clarify, each service only needs to be installed and run on the 
machine it's intended for. Therefore, the frontend service file
should only be installed, enabled, and started on the frontend machine
in its cluster.

## Scripts
The scripts that are intended to be run on clustered machines (i.e. directly 
on frontend, backend or dmz) are stored in the ```it490/bash/``` directory.

Scripts that are meant for the deployment server only are inside the 
```it490/deployment/``` directory.

#### Necessary Configuration:

- ```push_to_deployment.sh```

    - ```app_dir``` should point to the entire ```it490``` directory.
    - ensure that the deployment ip address is set properly

- ```update_listener.sh```
    - Must ensure that the install location of the it490 project on each machine 
    is communicated and set properly in the deployment server

- Double check that all ips are properly configured through
zerotier as well as in these scripts 