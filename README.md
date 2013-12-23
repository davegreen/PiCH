PiCH
====================
PiCentralHeating - A combo hardware/software project for a low cost central 
heating system controller, designed to run on the Raspberry Pi.

Objectives
---------------------
Our key objectives of the PiCH project are :

- To be able to control your heating from anywhere and
- Being able to define which area you want to be heat at a given time.

Obviously there are other products that enable you to do this, side objectives include:

- Low setup cost
- Small form factor
- Easily customisable
- Simple enough to use without a PC
- Scalable to lots of sensors
- Weather compensation

Description
---------------------
This software is written in a couple of different pieces. The first piece is written in Python and deals with core 
functionality, like reading and logging temperature. The second part is a web interface written in PHP and deals 
primarily with the user interface and management of the system.

If you have any improvements at all please go ahead and make them, then submit a pull request!
If you can't do that, at least [let us know](https://github.com/davegreen/PiCH/tree/master#contact) what you would like to see.

### Core functionality (Python)

- Reads data from the temperature sensors.
- Writes information about the sensors and temperature to a MySQL database.
- Manages the heating system, turning the boiler on and off to keep to the target temperature.
- Displays the temp (in degrees centigrade) to one decimal place on 3 7-segment LED displays (e.g. 19.8)

### Web interface

- Display current and historical temperature data in the web interface.
- Allow management and naming of the temperature sensors plugged in.
- Scheduling and rule creation for heating system control.
- Easy management of other system settings and features.

TODO
---------------------
See the wiki page: https://github.com/davegreen/PiCH/wiki/TODO

Setting Up
---------------------

Contact
---------------------

Thanks
---------------------
- [RaspberryPi](http://www.raspberrypi.org) - For making it possible.
- [Bootstrap](https://github.com/twbs/bootstrap) - For making things look good.
- [WiringPi](https://github.com/WiringPi) - For making it easy to use the GPIO pins.
