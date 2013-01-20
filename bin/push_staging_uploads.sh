#!/bin/bash
cd ~/Desktop/toessay/wordpress/wp-content;
scp -r uploads/* ben@benimmanuel.com:~/src/toessay/wordpress/wp-content/uploads/ ;
