#!/usr/bin/env bash

git pull origin master #Pull changes

npm run prod #Compile assets

php artisan key:generate #Log users out

php artisan cache:clear #Clear cache
composer dump-autoload