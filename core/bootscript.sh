#!/bin/bash

#use modprobe to load the kernel modules for sensor reading
modprobe w1-therm
modprobe w1-gpio

#set the gpio pins up for correct communication
#gpio -g mode 27 out
gpio export 27 out
gpio export 23 out
gpio export 24 out
gpio export 25 out
