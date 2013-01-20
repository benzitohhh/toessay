#!/bin/bash
cd ~/Desktop/toessay/db;
mysqldump wp-toessay-local -uroot -proot > wp-toessay-local.sql;
git add wp-toessay-local.sql;
git commit -a -m 'new local db dump';
git push;

ssh ben@benimmanuel.com \
   "\
   cd ~/src/toessay/db; \
   git pull; \
   sed 's/http:\/\/toessay.co.uk.ben/http:\/\/benimmanuel.com\/toe/g' wp-toessay-local.sql > temp.sql; \
   mysql -uroot -ppassword wp-toessay-rimu < temp.sql; \
   rm -rf temp.sql; \
   ";
