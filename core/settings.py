#io pin for the relay controlling the boiler doubled up for now, renamed to make more sense IOPIN = 27
ThermPin = 27

#SQL settings
SQLSERVER = "localhost" 
SQLUSER = "pich" 
SQLPASS = "centralheating" 
SQLDB = "pich" 
SQLPORT = 3306

#files for the temp sensors
base_dir = '/sys/bus/w1/devices/' 
device_file = '/w1_slave'

#shift register vars
_DATA = 25 
_DATACLK = 23 
_LATCH = 24
_DELAY = float(0.00)
