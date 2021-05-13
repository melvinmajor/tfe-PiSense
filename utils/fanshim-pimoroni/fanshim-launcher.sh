#!/bin/sh

cd ~/Git/fanshim-python
git pull
cd ~/Git/fanshim-python/examples
sudo ./install-service.sh --on-threshold 65 --off-threshold 50 --delay 2 --brightness 64 --preempt
