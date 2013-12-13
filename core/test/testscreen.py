#!/usr/bin/python

import glob
import time
import wiringpi
import settings
from time import sleep

_temp = []
_temp.append("01000000")#zero
_temp.append("01110011")#one
_temp.append("00100100")#2
_temp.append("00100001")#3
_temp.append("00010011")#4
_temp.append("00001001")#5
_temp.append("00001000")#6
_temp.append("01100011")#7
_temp.append("00000000")#8
_temp.append("00000001")#9
_DPTRUE = "0"
_DPFALSE = "1"


_buffer = "" #buffer for the shift outputs

#set the pins to output, then set them all to high, so that we should have blank 7 segments
io = wiringpi.GPIO(wiringpi.GPIO.WPI_MODE_SYS)
io.pinMode(settings._DATA,io.OUTPUT)
io.pinMode(settings._DATACLK,io.OUTPUT)
io.pinMode(settings._LATCH,io.OUTPUT)
io.digitalWrite(settings._DATA,1)
io.digitalWrite(settings._DATACLK,1)
io.digitalWrite(settings._LATCH,1)

def shiftout(outputstring):
	#this is the function that actually shifts its out, latch low, then clk line oscillates, low high per bit to the data line
        i = 0
        io.digitalWrite(settings._LATCH, io.LOW)
        print "latch off"
        while i < len(outputstring):
		#sleeps here, to slow it down 3hz data speed
#		sleep(.01)
                io.digitalWrite(settings._DATACLK,io.LOW)
                io.digitalWrite(settings._DATA,int(outputstring[i]))
		io.digitalWrite(settings._DATACLK,io.HIGH)
		i = i + 1
        io.digitalWrite(settings._LATCH, io.HIGH)
        io.digitalWrite(settings._DATA,io.HIGH)

	#latch needs to go high at the end, or the registers ignore the data
	print "latch on"

def outputshiftstring():
	global _buffer
	#output the string then clear the buffer
	shiftout(_buffer)
	_buffer = ""

while True:
	i =9
	while (i >= 0):
		_buffer = _temp[i] +_temp[i] +_temp[i] +_temp[i] +_temp[i] +_temp[i] 
		print _buffer
		outputshiftstring()
		i -= 1
		sleep(.5)
