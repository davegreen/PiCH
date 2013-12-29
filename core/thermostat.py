#!/usr/bin/python

from datetime import datetime, time
import wiringpi
import sys
from time import sleep
import MySQLdb
import settings

FINISHTIME = time(0,0,0)
DEVID = ''
TARGETTEMP = ''

if len(sys.argv) != 2:
	print "Usage: ./thermostat.py <scheduleid>"
	sys.exit()

SCHEDULEID = sys.argv[1]
io = wiringpi.GPIO(wiringpi.GPIO.WPI_MODE_SYS)
io.pinMode(settings.ThermPin,io.OUTPUT)


def UpdateHeatingStatus(status):
        db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT )
        cursor = db.cursor()
        #writing out the devid of the sensors, trys to update first, in the case of a dead sensor coming back online.
        
        try:
                sql = "INSERT INTO `status` (`status`) VALUES ('" + status + "');"
                cursor.execute(sql)
                db.commit()
                
        except:
        	db.rollback
        
        db.close


def RetrieveSQLOffset(devid):
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT)
	cursor = db.cursor()
	sql = "SELECT `offset` FROM `sensors` WHERE `uid` = '" + devid + "' LIMIT 1"
	cursor.execute(sql)
	result = cursor.fetchall()
	db.close
	return result[0]

def error_end(reason):
	#in case of an error, handle it by calling this function which kills
	#the sql connection, and sets the pin to low, closing the relay
	io.digitalWrite(settings.ThermPin,io.LOW)
	#db.close
	print reason
	sys.exit()

def read_temp_raw ():
	try:
		f = open(settings.base_dir + DEVID + settings.device_file, 'r')
		lines = f.readlines()
		f.close()
		return lines
	
	except:
		error_end("sensor dead")
	
def read_temp ():
	lines = read_temp_raw()
	i = 0
	
	while lines[0].strip()[-3:] != 'YES':
		sleep(0.2)
		lines = read_temp_raw()
		
		if i > 5:
			error_end("CRC Error")
		
		else :
			i = i + 1

	equals_pos = lines[1].find('t=')
	
	if equals_pos != -1:
		temp_string = lines[1][equals_pos+2:]
		offset = RetrieveSQLOffset(DEVID)
		temp_c = float(temp_string) / 1000.0
		temp_c = temp_c + offset[0]
		return temp_c

def update_SQL():
	global DEVID
	global TARGETTEMP
	global SCHEDULEID

	try:
		#open database connection
		db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT )
		
		# prepare a cursor object using cursor() method
		cursor = db.cursor()	
		SQL1 = "SELECT `targettemp`, `sensor` FROM `rules` WHERE `schedule` = " + SCHEDULEID
		SQL2 = "SELECT `timeend` FROM `schedules` WHERE `id` = " + SCHEDULEID	

		cursor.execute(SQL1)
		results = cursor.fetchall()
		DEVID = results[0][1]
		TARGETTEMP = results[0][0]
		cursor.execute(SQL2)
		results = cursor.fetchall()
		split_finish(results[0][0])	
		cursor.close()
		db.close()
	
	except:
		error_end("Schedule Removed????")
	
def split_finish(sqlfinish):
	global FINISHTIME
	t = str(sqlfinish).split(":")
	FINISHHOUR = int(t[0])
	FINISHMIN = int(t[1])
	FINISHSEC = int(t[2])
	FINISHTIME = time(FINISHHOUR,FINISHMIN,FINISHSEC)

update_SQL()
b = True

while b == True:
	update_SQL()	
	UpdateHeatingStatus(1)
	print "target temp " + str(TARGETTEMP) + " current temp " + str(read_temp())

	if float(TARGETTEMP) > float(read_temp()):
		print "temp is too low, switch on"
		io.digitalWrite(settings.ThermPin,io.HIGH)
		
	else:
		print "Temp is good, switch off"
		io.digitalWrite(settings.ThermPin,io.LOW)
	
	if datetime.time(datetime.now()) < FINISHTIME:
		print "time left to run"
		sleep(2)
	
	else:
		print "time to go :("
		b = False

print "switching off the heating now"
io.digitalWrite(settings.ThermPin,io.LOW)
UpdateHeatingStatus(0)
