# Stock Management

Stock management is a webapp made with symfony to help me track my stock.

## Functionalities

1. Define storages
2. Create the products you want to track
3. Create alerts to know what products are missing

## Requirements

- PHP 8.3 
- Symfony 7
- Min.io for the images
- A database (I use Sqlite3 for development)
- Symfony client (recommended)

## Installation

Clone this repository and install dependencies with 
````
composer install
composer dump-autoload
npm i
````

Create a bucket in minio, a set its name in the .env file and update the database connection string too.

## Run

Use this command (development mode only)
````
symfony serve
````

## Docker

Coming soon !

## Author

[@Namularbre](https://github.com/namularbre)
