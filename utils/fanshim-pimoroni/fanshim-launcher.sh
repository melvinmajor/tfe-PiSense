#!/bin/sh

cd ~/Git/fanshim-python
git pull
cd ~/Git/fanshim-python/examples
sudo ./install-service.sh --on-threshold 70 --off-threshold 55 --delay 2 --brightness 64 --preempt
