#!/bin/bash
gpio export 27 out 
gpio -g mode 27 out 
if [ -z "$2" ]; then /usr/bin/python ./thermostat.py $1 > output.log; else /usr/bin/python ./thermostat.py $1; fi

