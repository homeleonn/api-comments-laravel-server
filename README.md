
# Comments Api

## Go to the root folder of the project and let's go

## Project setup
```
composer install
composer run-script post-root-package-install
composer run-script post-create-project-cmd
```

## Create and configure your database

- Create mysql database
- Configure your .env for database connection

## Run migrations
```
php artisan migrate --seed
```

## Run php built-in web server
```
php -S localhost:8086 -t public/
```
**Good luck, friend**
