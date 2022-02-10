# Booking System

This is an experimental booking system using Laravel 5.7 and Vue 2.

## Prerequisites

- Git.
- PHP.
- Composer.
- Laravel CLI.
- MySQL/MariaDB.
- A webserver like Nginx or Apache.
- Node Package Manager.

## Setup

### Clone

```
$ git clone https://github.com/arthurvasconcelos/booking-system.git

$ cd booking-system
```

### DB Creation
```
$ CREATE DATABASE IF NOT EXISTS booking_system CHARACTER SET utf8 COLLATE utf8_general_ci;

$ CREATE USER IF NOT EXISTS 'booking_user'@'localhost' IDENTIFIED BY PASSWORD 'bookpwd';

$ GRANT ALL PRIVILEGES ON booking_system.* TO 'booking_user'@'localhost';
```

### Install
```
$ composer install

$ npm install
```

### Configs
-   .ENV
    ```
    $ cp .env.example .env
    ```
    and update db section
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=booking_system
    DB_USERNAME=booking_user
    DB_PASSWORD=bookpwd
    ```
-   Generate the application key
    ```
    $ php artisan key:generate
    ```
-   Install Passport
    ```
    $ php artisan passport:install
    ```
-   Migrate the application
    ```
    $ php artisan migrate
    ```
-   Seed Database
    ```
    $ php artisan db:seed
    ```

### Run the application
```
$ npm run prod

$ php artisan serve
```
