# racetrack-backend 

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)

Short description of your Laravel project.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features

-  List of features (e.g. what it does and key benefits)

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and npm (for Laravel Mix)
- Postgres DB

## Installation

1. Clone the repository:

    ```bash
    https://github.com/nativebrains/racetrack-backend
    ```

2. Install Composer dependencies:

    ```bash
    composer install
    ```

3. Create a copy of the `.env.example` file:

    ```bash
    cp .env.example .env
    ```

4. Generate the application key:

    ```bash
    php artisan key:generate
    ```

5. Configure your database and other settings in the `.env` file.
- Create a Postgres DB and connect in ENV. 
- Set the DB_USERNAME and DB_PASSWORD in the.env file.
- Set the APP_URL in the.env file.
- Set the MAIL_MAILER and MAIL_HOST in the.env file.
- Set the MAIL_USERNAME and MAIL_PASSWORD in the.env file.
- Set the MAIL_ENCRYPTION in the.env file.
- Set the MAIL_FROM_ADDRESS and MAIL_FROM_NAME in the.env file
- Set the redis cache if any.

6. Migrate the database:

    ```bash
    php artisan migrate
    ```

7. Install NPM dependencies:

    ```bash
    npm install
    ```

8. Compile assets:

    ```bash
    npm run dev
    ```

9. Serve the application:

    ```bash
    php artisan serve
    ```

10. Setup Meta Project information:

    ```bash
    php artisan sync:setup-project-meta-data
    ```

Visit `http://localhost:8000` in your browser.

## Configuration



## Usage

-  

## Contributing

-  

## License

This project is open-source and available under the [MIT License](LICENSE).

