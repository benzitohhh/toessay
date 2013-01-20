#!/bin/bash
cd ~/Desktop/toessay/wordpress/wp-content;
scp -r uploads/* toessayc@toessay.co.uk:~/www/wp-content/uploads/ ;
