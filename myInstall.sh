#!/bin/bash
git pull
yes | cp -arf issabelmodules/modules /var/www/html

function callRequest(){
    cp -rf novoipagi /var/lib/asterisk/agi-bin
    chmod -R 777 /var/lib/asterisk/agi-bin/novoipagi
    query="REPLACE INTO miscdests (id,description,destdial) VALUES('102','callRequest','6668')"
    # mysql -hlocalhost -uroot -p$rootpw asterisk -e "$query"
    # echo "**Queue callRequest Module Added." >> voiz-installation.log
    echo "* * * * *  root /usr/bin/php -q /var/www/html/modules/novoip-callreq/cron.php" > /etc/cron.d/novoip.cron
}
function featurecodes(){
cp -rf customdialplan/extensions_voipiran_featurecodes.conf /etc/asterisk/
# sed -i '/\[from\-internal\-custom\]/a include \=\> voipiran\-features' /etc/asterisk/extensions_custom.conf
# echo "" >> /etc/asterisk/extensions_custom.conf
# echo "#include extensions_voipiran_featurecodes.conf" >> /etc/asterisk/extensions_custom.conf
}


mysql -u root -p$rootpw -e 'CREATE TABLE IF NOT EXISTS `novoip_callrequests` ( `id` int(11) NOT NULL, `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL, `prefix` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL, `repeat` int(11) NOT NULL DEFAULT '2', `soundRepeat` int(11) NOT NULL DEFAULT '1', `insertDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `event` datetime NOT NULL, `status` tinyint(1) NOT NULL DEFAULT '1', `trunk` int(11) NOT NULL, `hook` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL, `destination` text COLLATE utf8mb4_unicode_ci NOT NULL, `callerID` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL, `reqNum` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
mysql -u root -p$rootpw -e 'CREATE TABLE IF NOT EXISTS `novoip_callrequests_phones` ( `id` int(11) NOT NULL, `number` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL, `exData` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL, `repeat` int(2) NOT NULL, `status` enum('wating','down','pending','') COLLATE utf8mb4_unicode_ci NOT NULL, `callDate` datetime NOT NULL, `uniqueID` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL, `CID` int(11) NOT NULL, `result` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
mysql -u root -p$rootpw -e 'ALTER TABLE `novoip_callrequests` ADD PRIMARY KEY (`id`), ADD KEY `trunk` (`trunk`)'
mysql -u root -p$rootpw -e 'ALTER TABLE `novoip_callrequests_phones` ADD PRIMARY KEY (`id`);'
mysql -u root -p$rootpw -e 'ALTER TABLE `novoip_callrequests` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'
mysql -u root -p$rootpw -e 'ALTER TABLE `novoip_callrequests_phones` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'

callRequest
featurecodes