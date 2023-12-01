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
# make sure database connection and database name on .env file is same as your environment tools
php artisan migrate:refresh --seed

# run project
php artisan serve
```
