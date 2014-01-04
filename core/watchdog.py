#!/usr/bin/python

import settings
import glob
import time
import MySQLdb 
import wiringpi
import settings
from time import sleep

_time = float(0)
_buffer = "" #buffer for the shift outputs

#this is the setup for the tempaurature array. it is currently called something stupid
#and it could probably do with being in a differnt file. not sure if that can be done though
_temp = []
_temp.append("1000000")#zero
_temp.append("1110011")#one
_temp.append("0100100")#2
_temp.append("0100001")#3
_temp.append("0010011")#4
_temp.append("0001001")#5
_temp.append("0001000")#6
_temp.append("1100011")#7
_temp.append("0000000")#8
_temp.append("0000001")#9
_DPTRUE = "0"
_DPFALSE = "1"

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
		
	while i < len(outputstring):
		io.digitalWrite(settings._DATACLK,io.LOW)
		sleep(settings._DELAY)
		io.digitalWrite(settings._DATA,int(outputstring[i]))
		io.digitalWrite(settings._DATACLK,io.HIGH)
		sleep(settings._DELAY)
		i = i + 1
			
	io.digitalWrite(settings._LATCH, io.HIGH)
	io.digitalWrite(settings._DATA,io.HIGH)
	#print outputstring
	#latch needs to go high at the end, or the registers ignore the data
	
def outputshiftstring():
	global _buffer
	#output the string then clear the buffer
	#print "outputting now " + _buffer
	shiftout(_buffer)
	_buffer = ""

def appendstring(inputnumber, decimalpoint):
	global _buffer
	#appends the information to the main output buffer. need to be careful as im not sure which way these will work when another set of SR are in circuit
	_buffer = _buffer + str(decimalpoint) + str(_temp[inputnumber])

def preptemp(inputtemp):
	
	if len(inputtemp) > 3:
		#take the temperature and split it out to the append string function
		appendstring(int(inputtemp[3]),_DPFALSE)
		appendstring(int(inputtemp[1]),_DPTRUE)
		appendstring(int(inputtemp[0]),_DPFALSE)

def read_temp_raw(devid):
	#try to read the temperature sensor, based on devid, if it fails, mark it as dead, else return the data.
	
	try:
	    f = open(settings.base_dir + devid + settings.device_file, 'r')
	    lines = f.readlines()
	    f.close()
	    return lines
	
	except:
		#not sure why this is commented out atm
		#SQL_MarkDeadSensor(devid)
		return "fail"

def RetrieveSQLOffset(devid):
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()
	sql = "SELECT `offset` FROM `sensors` WHERE `uid` = '" + devid + "' LIMIT 1"
	cursor.execute(sql)
	result = cursor.fetchall()
	db.close
	return result[0]

def read_temp(uid, devid):
	global _time
	#get data from the raw function
	lines = read_temp_raw(devid)
	
	if lines == "fail" :
		return 0
	
	else :
		i = 0
		#this is a CRC check, if it fails, it will try 5 times, then give up thinking it has a dead sensor
		
		while lines[0].strip()[-3:] != 'YES':
			time.sleep(0.2)
			lines = read_temp_raw(devid)
			
			if i > 5 :
				return 0
			
			else :
				i = i + 1
				
		#print lines	
		equals_pos = lines[1].find('t=')
		#check the tempurature for some weird shit, then log it out to SQL, and also prepare for the round of shifting out.
		#need to add in a little check, as not all sensors will shift out....
		result = SQL_Read_EnabledSensors(devid)
		
		if equals_pos != -1:
			temp_string = lines[1][equals_pos+2:]
			temp_c = float(temp_string) / 1000.0
			offset = RetrieveSQLOffset(devid)
			temp_c = temp_c + float(offset[0])
			
			if result > 0:
				preptemp(str(temp_c))
			
			if time.time() > _time + float(60):
				SQL_LogTemp(devid,uid,temp_c)
				_time = time.time()
		return 1

def read_device_list():
	#reads out the devices from the bus master files, writes them out to the sql DB
	f = open ("/sys/bus/w1/devices/w1_bus_master1/w1_master_slaves", "r" )
	lines = f.readlines()
	i = 0
	
	for each in lines :
		SQL_Write_DevID(lines[i][:15])
		i = i + 1
		
	return 0

def SQL_Write_DevID(devid):
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT )
	cursor = db.cursor()
	#writing out the devid of the sensors, trys to update first, in the case of a dead sensor coming back online.
	
	try:
		sql = "INSERT INTO `sensors` (`uid`, `enabled`) VALUES ('" + devid + "', '1');"
		cursor.execute(sql)
		db.commit()
		
	except:
		try:
			sql = "UPDATE `sensors` SET `enabled` =  '1' WHERE `uid` = '" + str(devid) + "'"
			cursor.execute(sql)
			db.commit()
			
		except:
			db.rollback
	
	db.close
	
def SQL_Read_EnabledSensors(devid):
	#this function uses SQL to get the DevIDs, and then read each tempurature sensor in turn.
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()
	sql = "SELECT `uid` FROM `sensors` WHERE `uid` = '" + devid + "' AND `enabled` = 1 AND `output` = 1 LIMIT 1"
	cursor.execute(sql)
	result = cursor.rowcount
	db.close
	return result

def SQL_Read_DevID():
	#this function uses SQL to get the DevIDs, and then read each tempurature sensor in turn.
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()
	sql = "SELECT `uid` , `device` FROM `sensors` WHERE `enabled` = 1 ORDER BY `sequence` ASC"
	cursor.execute(sql)
	results = cursor.fetchall()
	
	for row in results:
		uid = row[0]
		devid = row[1]
		# Now print fetched result
			
		if read_temp (str(devid),str(uid)) == 0 :
			SQL_MarkDeadSensor(devid)
			
	db.close
	outputshiftstring()
				
def SQL_LogTemp(devid, uid, temp):
	#as it says on the tin, connects to the SQL server, and puts in the data its passed
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()	
	sql = "INSERT INTO `temperature` (`sensor` ,`temperature` ,`timestamp`) VALUES ('" + devid + "','" + str(temp) + "',CURRENT_TIMESTAMP);"
	cursor.execute(sql)
	db.commit()
	data = cursor.fetchall()
	db.close
	return

def SQL_MarkDeadSensor(sensorID):
	#in the event that watchdog cant get the data out of a sensor(unplugged, or 5 CRC errors, or faulty) it calls this function
	#just connects to the SQL, and marks the sensor as disabled
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()
	sql = "UPDATE `sensors` SET `enabled` =  '0' WHERE `sensors`.`device` =" + str(sensorID) + " LIMIT 1"
	cursor.execute(sql)
	db.commit()
	data = cursor.fetchall()
	db.close
	return

while True:
	#this reads in the devices, and updates the SQL table, it does this every 5 times through the sensors, to automatically pick up new sensors	
	read_device_list()
	i = 0
	
	while i < 5:
		#read the sensors in from the sqlDB, write out the data back to SQL & shift registers, sit happily in the background, never crash, happy days
		SQL_Read_DevID()
		time.sleep(1)
		i = i + 1
