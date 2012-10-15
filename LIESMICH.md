READ ME
==========

Aim: Code for a weather station, which uses 2 sensors: BMP085(Bosch) and DHT22 to measure the enviroment and report back through a xBee Link to the base unit. The base unit will send the data to a database on a specific webserver. Here the data will be processed, and generate graphs.
All measure values will be shown at: http://chanhdat.us/?page_id=712 

Hardware:

1. 2 Arduino Uno R3

2. 2 Xbee Shield (using xBee S1 at the moment)

3. 1 Ethernet Shield

4. 1 BMP085 (using breakout of Sparkfun)

5. 1 AM2302 (wired DHT22) temperature-humidity sensor variant

Software/Server:

1. Host at hostfactory.ch (using my website address: chanhdat.us)

Plan:

1. Base unit: Arduino + Ethernet Shield + Xbee Shield
using BASE.c (upload to Adruino using its own software)

2. Remote unit: Arduino + Xbee Shield + Sensors
using REMOTE.c

3. Server site: using PHP to get data from Base unit and then drawing a graph of temperature, humidity and air pressure of the last 48 hours.

Building instruction:

1. Base unit: just put everything together and upload the code.
Don't forget to connect ICSP to Xbee Shield (Only pin 2, 5, 6: VTG (5v), RST (reset), GND (ground).

2.Remote unit: Connect sensors as http://chanhdat.us/wp-content/uploads/2012/03/Fixiert_bb.png
and then upload the code

3.Server site: get JPGRAPH (at jpgraph.net) and upload to host
Create a database (detailed instruction at chanhdat.us with Tag: Arbeitsjournal)

I released all these codes in MIT License (well, with Beerware isn't bad, is it?)