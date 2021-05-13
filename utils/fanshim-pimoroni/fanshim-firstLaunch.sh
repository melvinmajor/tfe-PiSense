#!/bin/sh

sudo apt-get update -y
sudo apt-get upgrade -y
sudo pip install fanshim
git clone https://github.com/pimoroni/fanshim-python.git ~/Git/fanshim-python
cd ~/Git/fanshim-python
sudo ./install.sh
cd ~/Git/fanshim-python/examples
sudo ./install-service.sh --on-threshold 65 --off-threshold 50 --delay 2 --brightness 64 --preempt
