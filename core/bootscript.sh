#!/bin/bash

#use modprobe to load the kernel modules for sensor reading
modprobe w1-therm
modprobe w1-gpio

#if you want to make these modules load on startup, you can run the following (as sudo/root)
#echo "w1-gpio" >> /etc/modules
#echo "w1-therm" >> /etc/modules

#set the gpio pins up for correct communication
#gpio -g mode 27 out
gpio export 27 out
gpio export 23 out
gpio export 24 out
gpio export 25 out
