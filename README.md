<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## **Job and freelancing portal**

this project for mobile application that enables companies to post job opportunities and allows job seekers to search and apply for
them.this project with different roles and permissions. Key features include.
<small>
- Register, Login, logout, Reset password, forgot password, and create a profile.
- Email verification is done via a verification code.
- Companies can post job listings with complete details and track and manage applications from job seekers, with the ability to accept or reject candidates.
- Job seekers can search for job opportunities and apply directly through the app.
- create and manage their resumes.
- The app provides suggested job opportunities and recommended companies within the same field the user is searching for.
- follow-up system between users (companies and job seekers).
- Job seekers can create posts that support text, images, and files.
- Users can report other users or posts, selecting a reason for the report.
- messaging between users.
- In addition to creating a real-time notification system for important alerts using Firebase.
 ##### **Admin Dashboard**
- Manage all opportunities, posts, and user accounts (edit, delete, and ban users using ban package).
- Review and respond to user reports.
- Role and permission management using the Spatie package.
- Add and manage employees accounts.
</small>

## **Team Members**
- **[ِ Amal](https://github.com/amalfathieh)**.
- **[Ali](https://github.com/AliMohammad92)**.

## **Introduction**

Project Title is a project that does something useful. It was created to solve a particular problem, and it provides a solution that is better than the alternatives.

## **Prerequisites**
You should have **`composer`** installed. If you don't install composer from here.

## **Installation**

To install Job and freelancing portal project, follow these steps:

1. Clone the repository: **`git clone https://github.com/amalfathieh/Jobs.git`**
2. Navigate to the project directory: **`cd jobs-and-freelancing-portal`**
3. Run this command to download composer packages:
    **composer install`**
4. Run this command to update composer packages:
    **`composer update`**
5. Create a copy of your .env file: **`cp .env.example .env`**
6. Generate an app encryption key: **`php artisan key:generate`**

7. Create an empty database for our application in your DBMS
8. In the .env file, add database information to allow Laravel to connect to the database
9. Migrate the database: **`php artisan migrate`**

10. Seed the database : **`php artisan db:seed`**
11. Start schedule  : **`php artisan schedule:work`**
12. Start queue : **`php artisan queue:work`**
13. Open up the server: **`php artisan serve`**
    
   
