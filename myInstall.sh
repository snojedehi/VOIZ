#!/bin/bash
git pull
yes | cp -arf issabelmodules/modules /var/www/html

function callRequest(){
cp -rf novoipagi /var/lib/asterisk/agi-bin
chmod -R 777 /var/lib/asterisk/agi-bin/novoipagi
query="REPLACE INTO miscdests (id,description,destdial) VALUES('102','callRequest','6668')"
mysql -hlocalhost -uroot -p$rootpw asterisk -e "$query"
echo "**Queue callRequest Module Added." >> voiz-installation.log
}
callRequest