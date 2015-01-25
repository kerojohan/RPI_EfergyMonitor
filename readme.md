## RPI_EfergyMonitor

UNDER DEVELOPMENT

Just using your original Efergy product and a Raspberry Pi you will be able to monitor your consumption, have statistics and know how much kWh did you waste.

Checked in a Raspberry Pi.

![Real Time Consumption](https://github.com/kerojohan/RPI_EfergyMonitor/blob/master/screenshots/realtimeconsumption.png)

Requeriments:

Just to get Laravel working:
Apache 
Php5
Mysql

To take data:
Download my fork [RPI_Efergy](https://github.com/kerojohan/RPI_Efergy), that will store some data directly into your Mysql.

How to start capturing data?

modprobe -r dvb_usb_rtl28xxu;
rtl_fm -f 433.54e6 -s 200000 -r 96000 -A fast 2>/dev/null | /home/pi/scripts/RPI
_Efergy/EfergyRPI_mysql;

note that 433.54e6? thats my best freq, you will have another, check the RPI_Efergy readme to know it.


more screenshots

![Statistics](https://github.com/kerojohan/RPI_EfergyMonitor/blob/master/screenshots/statistics.png)

![Historic Data](https://github.com/kerojohan/RPI_EfergyMonitor/blob/master/screenshots/historicdata.png)

### License

Open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

Almost done but I need help to finnish it, by the moment only in catalan and prices of kWh are hardcoded, planninng to do virtual bills and of course the configuration area where to put or choose a price according your energy provider
