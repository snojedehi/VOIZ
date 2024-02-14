#!/bin/bash

yes | cp -rf issabelpbxmodules/bulkdids /var/www/html/admin/modules/

function callRequest(){
cp -rf novoipagi /var/lib/asterisk/agi-bin
chmod -R 777 /var/lib/asterisk/agi-bin/novoipagi
query="REPLACE INTO miscdests (id,description,destdial) VALUES('102','تماس خودکار','6668')"
mysql -hlocalhost -uroot -p$rootpw asterisk -e "$query"
echo "**Queue callRequest Module Added." >> voiz-installation.log
}
callRequest