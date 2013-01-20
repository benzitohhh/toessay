#!/bin/bash

#dump
#cd ~/Desktop/toessay/db;
#mysqldump wp-toessay-local -uroot -proot > dump-local.sql;

#scp
#scp dump-local.sql toessayc@toessay.co.uk:~/toessay/db/dump-local.sql;
#rm -rf dump-local.sql;

ssh toessayc@toessay.co.uk \
   "\
    cd ~/toessay/db; \
    #backup \
    mkdir -p backups; \
    #day=$(( `date +%s` )); \
    #mysqldump -utoessayc -pNelsonmandela123! toessayc_wp-toessay-prod > backups/wp-toessay-prod-backup-$day.sql; \
    \
    #convert and apply \
    #sed 's/http:\/\/toessay.co.uk.ben/http:\/\/www.toessay.co.uk/g' dump-local.sql > dump-local-prod.sql; \
    #mysql -utoessayc -pNelsonmandela123! toessayc_wp-toessay-prod < dump-local-prod.sql; \
    \
    #cleanup \
    #rm -rf dump*; \
   ";

