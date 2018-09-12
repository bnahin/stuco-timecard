#!/usr/bin/env bash

#git stash #Save local changes
sudo git reset --hard #Overwrite local changes

git pull origin master #Pull changes
#git stash apply --index #Apply local changes

sudo chown -R ec2-user:apache public/
npm run prod #Compile assets

php artisan key:generate #Log users out
php artisan migrate #Migrate new tables

php artisan cache:clear #Clear cache

composer install
composer dump-autoload