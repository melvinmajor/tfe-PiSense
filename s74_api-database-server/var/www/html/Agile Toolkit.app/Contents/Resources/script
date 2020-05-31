#!/bin/bash
if [ "$1" ]; then
    cd $1
else
    echo "Now Drag & Drop your 'agiletoolkit' folder on top of this window."
    exit;
fi


echo "Welcome to Agile Toolkit. Running 'php -S localhost:8888' in `pwd`"
echo

(sleep 1 && open http://localhost:8888) &
php -S localhost:8888
