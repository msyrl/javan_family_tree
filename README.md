### Installation

Set environment on **.env** file with copy the **.env.example**

```sh
cp .env.example .env
```

Then run this following command

```sh
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```
