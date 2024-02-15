#!/bin/bash
git pull
yes | cp -arf issabelmodules/modules /var/www/html

function callRequest(){
    cp -rf novoipagi /var/lib/asterisk/agi-bin
    chmod -R 777 /var/lib/asterisk/agi-bin/novoipagi
    query="REPLACE INTO miscdests (id,description,destdial) VALUES('102','callRequest','6668')"
    # mysql -hlocalhost -uroot -p$rootpw asterisk -e "$query"
    echo "**Queue callRequest Module Added." >> voiz-installation.log
    echo "* * * * *  root /usr/bin/php -q /var/www/html/modules/novoip-callreq/cron.php" >> /etc/cron.d/novoip.cron
}
function featurecodes(){
cp -rf customdialplan/extensions_voipiran_featurecodes.conf /etc/asterisk/
# sed -i '/\[from\-internal\-custom\]/a include \=\> voipiran\-features' /etc/asterisk/extensions_custom.conf
# echo "" >> /etc/asterisk/extensions_custom.conf
# echo "#include extensions_voipiran_featurecodes.conf" >> /etc/asterisk/extensions_custom.conf
}
callRequest
featurecodes