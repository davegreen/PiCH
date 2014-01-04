#!/bin/bash

gpio export 27 out
gpio -g mode 27 out

kill -9 `/usr/bin/pgrep -o -f ./thermostat.py`
/usr/bin/python killtherm.py