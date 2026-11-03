# Restaurant Review System

A web-based Restaurant Review System developed using PHP, MySQL, HTML, CSS and JavaScript. The application allows users to discover restaurants, submit reviews and manage their feedback.

## Features

- User registration and login
- Restaurant review submission
- Edit and delete reviews
- User dashboard
- Feedback management
- Restaurant images and slideshow
- Form validation

## Technologies Used

- PHP
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- XAMPP
- phpMyAdmin

## Installation Guide

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/restaurant_review.git
```

### 2. Move the project

Place the project folder inside:

```text
C:\xampp\htdocs\
```

### 3. Create the database

1. Start Apache and MySQL in XAMPP.
2. Open `http://localhost/phpmyadmin`.
3. Create a database named `restaurant_review`.
4. Import the provided `restaurant_review.sql` file.

### 4. Configure database connection

Update `db.php` with your local database credentials.

### 5. Run the application

Open your browser and visit:

```text
http://localhost/restaurant_review/
```

## Project Structure

```text
restaurant_review/
├── images/
├── uploads/
├── js/
├── about.php
├── add_review.php
├── dashboard.php
├── db.php
├── delete_review.php
├── edit_review.php
├── feedback.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── style.css
└── README.md
```

## Database

Database: `restaurant_review`

Database management: phpMyAdmin

## Developer

Developed as a web application project.