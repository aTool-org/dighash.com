#!/bin/sh
#git update
cd /home/wwwroot/dighash/hash_text

git pull origin master

cd /home/wwwroot/dighash/

\cp -rf hash_text/* ./

chmod -R 777 /home/wwwroot/dighash/tmp/

echo "update success"