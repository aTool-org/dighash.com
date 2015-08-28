#!/bin/sh
#git update
cd /home/wwwroot/default/hash_text

git pull origin master

cd /home/wwwroot/default/

\cp -rf hash_text/* ./

echo "git webhooks update success"