#!/bin/bash

#pull
cd  ~/Desktop/toessay/wordpress/wp-content;
scp -r toessayc@toessay.co.uk:~/www/wp-content/uploads/* uploads/ ;
