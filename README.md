## About Project

this project develop using laravel framework. this project is only for education & practice purpose.

## run project

```bash
# download all needed package from composer
composer install

# make .env file from .env.example
cp .env .env.example

# generate new key
php artisan key:generate

# migrate all tables with seeder
# make sure database connection and database name on .env file is same as your local environment
php artisan migrate:refresh --seed

# run project
php artisan serve
```

## run testing
```bash
# run auto testing
php artisan test

# run testing on specific test class
php artisan test --filter=ClassName
```

## documentation

documentations is on the folder "documentation". documentations created using postman. to using it you need to import both env and collection. make sure to activate the imported env on postman environments.

## other notes

maybe you will have problem when try to store image/media. make sure to update your php.ini file on your local environment

```bash
# to check php.ini location
php --ini
```

also make sure below variables on php.ini file are up to date with your need

```
memory_limit = 128M
upload_max_filesize = 30M
post_max_size = 100M
```
