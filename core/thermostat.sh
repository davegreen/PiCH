#!/bin/bash
gpio export 27 out 
gpio -g mode 27 out 

/usr/bin/python ./thermostat.py $1 > output.log
