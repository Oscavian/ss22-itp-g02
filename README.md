
A Project I developed together with 4 other colleagues from UAS Technikum Wien during summer semester 2022.
The goal of the project was to develop a **web based chat & assignment app for primary schools**.

The application is divided into two main components: A **Remote Procedure Call (RPC) API** written in **PHP** that processes method calls as POST requests and responds respectively.
The requests are sent by the frontend written in **jQuery** via AJAX calls. The User Interface is written in HTML/CSS using the Bootstrap 5 library and jQuery.

> ### Important Notice
> This application is explicitly NOT meant for production.
> It has been written by 5 students as their first collaborative project, thus some security measures may not have been taken care of.

## Features
_Work in progress_

## Repository Structure

- **backend/** - PHP RPC API
- **backend/db** - database interface
- **backend/logic** - classed & methods that process method calls.
- **backend/models** - classes representing entities and making database operations
- **backend/requestHandler.php** - entry point for frontend requests
- **client/** - HTML + JS/jQuery Frontend
- **client/assets** - resources, images, stylesheets
- **client/js** - JavaScript files, AJAX calls
- **client/pages** - HTML templates
- **uploads/** - user uploads, e.g. submissions for assignments

## Deployment

### Tech-Stack:

- **Apache** Web Server for hosting the site
- **PHP** powers the RPC API
- **MySQL/MariaDB** database for permanent data storage

### Requirements

- **PHP** 7.3+
- **MariaDB** 10.4+
- **Apache** 2.4.x+
- **jQuery** 3.6+ (bundled via CDN)
- **Bootstrap** 5+ (bundled via CDN)

## Local Setup

### 1. Clone the repository
Clone the repository into the htdocs folder of your local web server 
```
git clone https://github.com/Oscavian/ss22-itp-g02.git 
```

### 2. Setup the Database
- create a new database with a name of your choice
- import the provided SQL script into the database
- copy the contents of ``backend/db/dbaccess-template.php`` into ``backend/db/dbaccess.php``
- Fill in your database credentials into the provided constants

### 3. Done!
- navigate to localhost/ss22-itp-g02
- register a new teacher account or use the provided default credentials