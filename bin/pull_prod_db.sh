#!/bin/bash

#dump
ssh toessayc@toessay.co.uk \
   "\
   cd ~/toessay/db; \
   mysqldump -utoessayc -pNelsonmandela123! toessayc_wp-toessay-prod > dump-prod.sql;
   ";

#pull
cd  ~/Desktop/toessay/db;
scp toessayc@toessay.co.uk:~/toessay/db/dump-prod.sql ./;

# make local backup
DAY=$(( `date +%Y-%m-%d-%H%M-%s` ));
BACKUPFILE=backups/wp-toessay-local-backup-$DAY.sql;
mysqldump wp-toessay-local -uroot -proot > $BACKUPFILE;

# apply
sed 's/http:\/\/www.toessay.co.uk/http:\/\/toessay.co.uk.ben/g' dump-prod.sql > dump-prod-local.sql;
mysql wp-toessay-local -uroot -proot < dump-prod-local.sql;

# cleanup
rm -rf dump*;
ssh toessayc@toessay.co.uk \
   "\
   cd ~/toessay/db; \
   rm -rf dump-prod.sql;
   ";
