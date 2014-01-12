#!/usr/bin/python

from datetime import datetime, time
import sys
from time import sleep
import MySQLdb
import settings

if sys.argv[2] == "":
	print "Usage: ./stats.py <value> <module>"
	sys.exit()

def UpdateStats(Load, Module):
	db = MySQLdb.connect(host=settings.SQLSERVER,user=settings.SQLUSER,passwd=settings.SQLPASS,db=settings.SQLDB,port=settings.SQLPORT )
	cursor = db.cursor()
	
	try:

		sql = "INSERT INTO `pich`.`stats` (`value`, `module`) VALUES ('" + sys.argv[1] + "', '" + Module + "');"
		cursor.execute(sql)
		db.commit()
	
	except:
		db.rollback
		db.close
		
def error_end(reason):
	print reason
	sys.exit()


UpdateStats(sys.argv[1], sys.argv[2])
