# Crowdfund

A full-stack crowdfunding platform built with object-oriented PHP, MySQL and PDO.

Crowdfund allows creators to launch and manage campaigns while users can discover projects, select rewards, make pledges and interact through comments.

## Features

* User registration and authentication
* Secure password hashing
* Create, edit and delete crowdfunding projects
* Browse and search campaigns
* Project categories
* Campaign funding goals and progress tracking
* Project image uploads
* Reward tiers
* Reward availability and quantities
* User pledges
* Automatic project funding updates
* Project comments
* User profiles
* Contribution history
* Responsive interface
* Object-oriented PHP architecture
* Reusable PDO database connection
* Relational MySQL database

## Tech Stack

**Backend**

* PHP
* Object-Oriented Programming
* PDO
* MySQL

**Frontend**

* HTML
* CSS

**Database**

* MySQL
* Primary and foreign keys
* One-to-many relationships
* Many-to-many relationships

## Database Structure

The platform uses six main tables:

* `users`
* `categories`
* `projects`
* `rewards`
* `pledges`
* `comments`

A user can create multiple projects, projects can contain multiple rewards and comments, and users can support multiple projects through pledges.

## Project Architecture

```text
crowdfunding/
├── Classe/
│   ├── CRUD.php
│   ├── Category.php
│   ├── Comment.php
│   ├── Pledge.php
│   ├── Project.php
│   ├── Reward.php
│   └── User.php
├── assets/
├── css/
├── includes/
├── index.php
├── projects.php
├── project.php
├── project-create.php
├── project-edit.php
├── project-delete.php
├── reward-create.php
├── pledge-create.php
├── comment-create.php
├── profile.php
├── login.php
└── register.php
```

## Object-Oriented Architecture

The project uses a reusable `CRUD` class built on PDO.

The main entities extend the shared database functionality while providing methods specific to their responsibilities.

```text
PDO
 ↑
CRUD
 ↑
├── Project
├── User
├── Category
├── Reward
├── Pledge
└── Comment
```

This architecture keeps database operations reusable while separating the logic of each entity.

## Running Locally

### 1. Clone the repository

```bash
git clone https://github.com/YOUR-USERNAME/crowdfund-php.git
```

### 2. Open the project

```bash
cd crowdfund-php
```

### 3. Create the database

Import the included:

```text
crowdfunding.sql
```

into MySQL.

### 4. Start PHP

```bash
php -S localhost:8000
```

### 5. Open the application

```text
http://localhost:8000
```

## Demo Account

```text
Email: justin@test.com
Password: demo1234
```

The demo account is provided for local testing purposes.

## Purpose

Crowdfund was created to explore the architecture behind crowdfunding platforms while applying object-oriented PHP, relational database design, CRUD operations and reusable backend components.

The project focuses on building a complete and functional web application where multiple entities interact through meaningful database relationships.

## Future Improvements

Potential future improvements include payment processing, richer campaign media management, creator analytics, campaign updates and notification systems.
