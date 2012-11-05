## DB

Get latest local dump, push to git:

    cd db
    mysqldump wp-toessay-local -uroot -proot > wp-toessay-local.sql
    git commit -a -m 'new local db dump'
    git push

On the server, pull and wipe the db:

    cd db
    git pull
    sed "s/http:\/\/toessay.co.uk.ben/http:\/\/benimmanuel.com\/obi/g" wp-toessay-local.sql > temp.sql
    mysql -uroot -ppassword wp-toessay-rimu < temp.sql
    rm -rf temp.sql
    
