#!/bin/sh
cd /home/wwwroot/default/hash-text

git pull origin master

cd /home/wwwroot/default/

\cp -rf hash-text/* ./

echo "git webhooks update success"