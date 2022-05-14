#!/bin/bash


cd /var/www/crowphp

composer install --no-interaction

nodemon --exec php examples/index.php
