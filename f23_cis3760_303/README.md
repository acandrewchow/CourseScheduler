# F23_CIS3760_303

<a name="readme-top"></a>

# Contributors

- Andrew Chow
- Benjamin Turner-Theijsmeijer
- Quinn Meiszinger
- Darren Van Helden
- Ethan Scruton
- Noureldeen Ahmed
- Vrushangkumar Patel

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

Full-stack web app that allows users to plan and search for courses offered at the University of Guelph

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?logo=php)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.31x-orange?logo=laravel)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- GETTING STARTED -->
## Getting Started

Follow these steps to get started with the course scheduler web-app

### Prerequisites

1. **Download PHP via Windows:**
   - Visit the [official PHP downloads page](https://windows.php.net/download) on windows.php.net.
2. **Verify Installation:**
*   ```bash
     php -v
     ```

1. **Download PHP via MacOS:**
      ```bash
     brew install php
     ```
2. **Verify Installation:**
*   ```bash
     php -v
     ```
To download Laravel you will need PHP and Composer installed. Composer is a dependency manager for PHP and can be downloaded and installed [here](https://getcomposer.org/). Once PHP and Composer are installed, simply run the command: `composer global require "laravel/installer"`. This will install Laravel globally on your system, and allow you to use the `laravel` command. Also, when you have the repo downloaded, navigate to `laravel-app` and run `composer install` to install all the required dependencies. If you encounter any issues, the documentation for installing Laravel can be found [here](https://laravel.com/docs/10.x#meet-laravel).

### Installation


1. Clone the repository: 
```sh
    git clone https://gitlab.socs.uoguelph.ca/cis3760_f23/f23_cis3760_303.git
```

2. When you pull the repo you should see the following file structure in `laravel-app`:  
The first thing you should do is create a new `.env` file in the app root (lauravel-app directory), and copy everything from `.env.example` into it. You will have to go through the new `.env` file and configure your database settings. Here is an example of my local .env setup to use XAMPP MySQL: The most important file in the app is `artisan`. It is a PHP program that allows you to easily handle a lot of functions for the app. It can be used to create new controllers, start a local server, initialize the database, etc...

3. Laravel includes a way to easily initialize the database. If you navigate to `database/migrations/`, you will see a list of files that define each table in our database. Each file includes an `up()` and `down()` function that creates and drops the table respectively. 

To initialize the database, simply run `php artisan migrate:fresh` from the laravel-app directory. This will drop all existing tables in `cis3760` and recreate them. This command also re-initializes the database from CourseList.csv. To see more about database migration, the Laravel documentation is [here](https://laravel.com/docs/10.x/migrations)

## Important Files:

### `app`
This folder contains the server logic for our app. For example, `Courses.php` and `CoursesTaken.php` are two models in the app directory that interact with their respective database tables. Additionally, under the `Http` folder, the various controllers and middleware is included.
### `database`
As mentioned previously, this folder contains the files used to migrate each table in the database
### `public`
This folder contains all the publicly available resources. It contains the excel spreadsheet, javascript, css, and images.
### `resources`
This folder contains various resources used by the server. Specifically, this folder contains all of the various pages under `views`. Laravel uses a template engine called Blade. All of our previous pages have been changed to `.blade.php` files. To read more about Blade Templates see the documentation [here](https://laravel.com/docs/10.x/blade#introduction).
### `routes`
This folder contains the files that define how routes are processed by our app. `web.php` defines the routes used when viewing our page from the web, and `api.php` defines the REST endpoints that our api uses. The api routes each define the type of request, and appropriate controller function to pass the request to.
> Laravel uses a framework called Eloquent for interacting with the database. The models, controllers, and views are all defined using Eloquent standards. I recommend reading the documentation on Eloquent to familiarize yourself and see what it is capable of [here](https://laravel.com/docs/10.x/eloquent#generating-model-classes.)

# Running Local server with Laravel
Ensure you have your sql server on (xxamp/mamp/etc..) 

To run the server locally use the command `php artisan serv` from the laravel-app directory

<p align="right">(<a href="#readme-top">back to top</a>)</p>