#!/usr/bin/python
import wiringpi
import sys, getopt
import settings

io = wiringpi.GPIO(wiringpi.GPIO.WPI_MODE_SYS)
io.pinMode(settings.ThermPin,io.OUTPUT)

def error_end(reason):
	#in case of an error, handle it by calling this function which kills
	#the sql connection, and sets the pin to low, closing the relay
	io.digitalWrite(settings.ThermPin,io.LOW)
	#db.close
	print reason
	sys.exit()

print "switching off the heating now"
io.digitalWrite(settings.ThermPin,io.LOW)
