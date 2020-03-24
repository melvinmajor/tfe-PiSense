# Installation process for SDS011 (sds011test.py)

* `sudo apt install git-core python-serial python-enum lighttpd`
* `dmesg` and check the line with interface ttyUSB0 (normal last line)

In order for the sds011test.py script to run without errors, some changes:

* `sudo chown pi:pi /var/www/html/`
* `echo [] > /var/www/html/aqi.json`
* `chmod +x sds011test.py`
* Script is now runnable: `./sds011test.py`

# Installation process for SDS011 with screen (sds011test-withscreen.py)

* `sudo apt-get install python-numpy python-scipy python-matplotlib`
* `sudo pip3 install sds011` & `sudo pip install sds011`
* `sudo pip3 install pylab`
