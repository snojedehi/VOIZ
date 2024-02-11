#!/bin/bash

function bulkdids(){
if [ ! -d "/var/www/html/admin/modules/bulkdids" ]; then
#BULK DIDs Module
yes | cp -rf issabelpbxmodules/bulkdids /var/www/html/admin/modules/
amportal a ma install bulkdids
fi
    echo "**Bulk DIDs Module Added." >> voiz-installation.log
}

##Install Bulk DIDs Module
bulkdids

  COUNTER=$(($COUNTER+10))
    echo ${COUNTER} 