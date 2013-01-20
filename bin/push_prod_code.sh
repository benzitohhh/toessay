#!/bin/bash

ssh toessayc@toessay.co.uk \
   "\
   cd ~/toessay; \
   git pull; \
   cp -R wordpress/* ../www/ ; \
   ";
