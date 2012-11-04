### DB

Get latest local dump, push to git:

    cd db
    mysqldump wp-toessay-local -uroot -proot > wp-toessay-local.sql
    git add, commit, push

On the server, pull and wipe the db:

    cd db
    git pull
    mysql -uroot -ppassword wp-toessay-rimu < wp-toessay-local.sql

