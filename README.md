# Library-Website
Library website mostly backend development using PHP, and phpmyadmin database.

This website allows the user to login/register an account. The user can then search for books by title, author or category, reserve books, cancel reservations, view reserved books, and logout.
The user login details are stored in the database. Books and reservations are also stored. The passwords are stored using **secure hashing** meaning the password cannot be viewed from the database.
Server-side validation is used to ensure usernames are unique and phone numbers have the right amount of digits.

A PHP web application built with MySQL and hosted locally using XAMPP. Includes user authentication, session handling, and database‑driven features. This repository contains the source code and SQL export needed to run the project locally.

The purpose of this project is to learn backend development and PHP, use a database and host it on a server. To use server side validation, password security when stored in the database and configurig database connection.

If you want to run this project locally you need **XAMPP** (apache + MySQL) and phpmyadmin (included with XAMPP).

You can download XAMPP from: https://www.apachefriends.org/

### Place the project folder inside the XAMPP htdocs folder.

### Import the database:
1. Open phpMyAdmin:  
   http://localhost/phpmyadmin

2. Click **New** → create a database  
   Example name: `library_db`

3. Select the database → click **Import**

4. Choose the SQL file included with the project  
   (e.g., `library.sql`)

5. Click **Go** to import the tables and data


### Configure the database connection

Open the project's database configuration file **database.php** and make sure the settings match your XAMPP setup:

```php
$servername = "localhost";
$username = "root";
$password = "";   // XAMPP default has no password


To run the website, make sure apache and mySQL are running and then in your browser go to: http://localhost/Library-Website/

Project structure:
Library-Website/
|--pages/
|-- database/
|-- css/
|-- register.js
|-- README.md






