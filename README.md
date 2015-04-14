To install the application first the update the apllication by running composer

php composer update

Load the random words by running:

php app/console doctrine:fixtures:load

Notice that assetic is used so dump the files first

php app/console cache:clear
php app/console assets:install web
php app/console assetic:dump











