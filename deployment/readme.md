## Services

In this folder are the service unit files that define the parts of 
this application as systemd services

For purposes of the project they are stored here, however, in order to function properly they must be moved into ```/etc/systemd/system/``` and enabled through systemctl as such:

```
systemctl enable <service-name>
systemctl start <service-name>
```
Where ```<service-name>``` is the name of the service, in this case either ```backend```, ```frontend```, or ```dmz```.

The status of a running service can be viewed with:

```
systemctl status <service-name>
```

## Scripts

```push_to_deployment.sh``` must be configured to work properly.

```app_dir``` should point to the entire ```it490``` directory.

