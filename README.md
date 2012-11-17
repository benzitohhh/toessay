## DB

Get latest local dump, push to git:

    cd db
    mysqldump wp-toessay-local -uroot -proot > wp-toessay-local.sql
    git commit -a -m 'new local db dump'
    git push

On the server, pull and wipe the db:

    cd db
    git pull
    sed "s/http:\/\/toessay.co.uk.ben/http:\/\/benimmanuel.com\/toe/g" wp-toessay-local.sql > temp.sql
    mysql -uroot -ppassword wp-toessay-rimu < temp.sql
    rm -rf temp.sql


## wp-config.php

In /wordpress/, copy `wp-config.backup.php` to `wp-config.php` and set the db credentials.

## Permalinks

Ensure
* site directories (and any simlinks - i.e. in /var/www/ ) are readable by Apache. i.e. `sudo chgrp -R www-data wordpress`
* Apache mod_rewrite enabled
* Apache virtual host: `FollowSymLinks` option enabled, `FileInfo` directives allowed (e.g. `AllowOverride FileInfo` or `AllowOverride All`)
* .htaccess file is present within /wordpress/ and writeable. Below is an example. If url is not root, (i.e. bla.com/bla/),
set the `RewriteBase` and `RewriteRule` appropriately:

###

    # BEGIN WordPress
    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
    </IfModule>
    # END WordPress

## wp-admin

Make sure the following are visible (from "post" page, click on "screen options" on top bar):
* Categories
* Author

## Category (aka "issue", i.e. a collection of essays)
* `Admin Category`: create category (i.e. "issue4" etc..), add metadata (i.e. "Issue 4 - the maoism issue", "picture of mao", ..)
* `Admin Post`: assign post to category (i.e. "issue4")

Metadata functionality is provided by the Category Meta plugin (docs here: [http://wordpress.org/extend/plugins/wp-category-meta/](http://wordpress.org/extend/plugins/wp-category-meta/installation/) ).

To create new category metadata fields (i.e. "title", "date", "picture", etc..): `Admin Settings` -> `Category Meta`
