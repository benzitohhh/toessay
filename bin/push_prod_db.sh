#!/bin/bash

#dump
cd ~/Desktop/toessay/db;
mysqldump wp-toessay-local -uroot -proot > dump-local.sql;

#scp
scp dump-local.sql toessayc@toessay.co.uk:~/toessay/db/dump-local.sql;
rm -rf dump-local.sql;

DAY=$(( `date +%Y-%m-%d-%H%M-%s` ));
BACKUPFILE=backups/wp-toessay-prod-$DAY.sql;

ssh toessayc@toessay.co.uk \
   "\
    cd ~/toessay/db; \
    mysqldump -utoessayc -pNelsonmandela123! toessayc_wp-toessay-prod > $BACKUPFILE; \
    \
    sed 's/http:\/\/toessay.co.uk.ben/http:\/\/www.toessay.co.uk/g' dump-local.sql > dump-local-prod.sql; \
    mysql -utoessayc -pNelsonmandela123! toessayc_wp-toessay-prod < dump-local-prod.sql; \
    \
    rm -rf dump*; \
   ";

