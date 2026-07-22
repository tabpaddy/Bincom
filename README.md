# Bincom Election Result Management System

A Laravel 12 + Livewire 4 web application built as part of the **Bincom ICT Solutions Software Developer Technical Assessment**.

The application provides a user-friendly interface for viewing and managing election results from polling units across Delta State, Nigeria, using the supplied `bincom_test.sql` database.

---

## Project Overview

The objective of this assessment is to demonstrate the ability to build a simple but well-structured web application that interacts with an existing relational database.

The application implements the following three requirements:

1. Display election results for an individual polling unit.
2. Display the summed total result of all polling units under a selected Local Government Area (LGA).
3. Store election results for all political parties for a newly created polling unit.

The project was built using Laravel's modern ecosystem while maintaining compatibility with the supplied database schema.

---

# Technologies Used

- Laravel 12
- Livewire 4
- Tailwind CSS
- MySQL
- PHP 8.2+

---

# Features

## Question 1 – Polling Unit Result

Displays election results for an individual polling unit.

### Implemented Features

- Select Local Government Area (LGA)
- Automatically load Wards belonging to the selected LGA
- Automatically load Polling Units belonging to the selected Ward
- Display polling unit information
- Display all political party results
- Display total votes
- Responsive design
- Live updates using Livewire without page refresh

---

## Question 2 – LGA Result Summary

Displays the summed election results of every polling unit within a selected Local Government.

### Implemented Features

- Select any LGA
- Sum all polling unit results
- Group results by political party
- Display total votes
- Does **NOT** use the `announced_lga_results` table as instructed

---

## Question 3 – Create Polling Unit Result

Allows creation of a new polling unit together with election results for all political parties.

### Implemented Features

- Select LGA
- Select Ward
- Enter Polling Unit Number
- Enter Polling Unit Name
- Optional Description
- Enter scores for every political party
- Save Polling Unit
- Save all party results
- Database Transaction for data integrity
- Validation
- Success notification

---

# Project Structure

```
app/
│
├── Models/
│   ├── Lga.php
│   ├── Ward.php
│   ├── PollingUnit.php
│   ├── Party.php
│   └── AnnouncedPuResult.php
│
resources/
│
├── views/
│   ├── components/
│   │   └── layouts/
│   │       └── app.blade.php
│   │
│   └── livewire/
│       ├── polling-unit-result.blade.php
│       ├── lga-result.blade.php
│       └── create-result.blade.php
```

---

# Database Relationships

```
LGA
 │
 └── hasMany
      │
     Ward
       │
       └── hasMany
            │
        Polling Unit
             │
             └── hasMany
                  │
          Announced PU Results
```

---

# Installation

## Clone Repository

```bash
git clone https://github.com/tabpaddy/Bincom.git
```

```
cd bincom-election
```

---

## Install Dependencies

```bash
composer install
```

```bash
npm install
```

---

## Environment

Copy the example environment file.

```bash
cp .env.example .env
```

Generate the application key.

```bash
php artisan key:generate
```

---

## Database

Create a MySQL database.

Example:

```
bincom_db
```

Import the supplied SQL file.

```
bincom_test.sql
```

Update your `.env`

```
DB_DATABASE=bincom_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Run Application

```bash
php artisan serve
```

Compile assets.

```bash
npm run dev
```

Visit

```
http://127.0.0.1:8000
```

---

# Design Decisions

## Laravel Livewire

Instead of using JavaScript AJAX requests, Livewire was used to build the dependent dropdowns.

Benefits include:

- Less JavaScript
- Reactive UI
- Cleaner code
- Server-side rendering
- Better Laravel integration

---

## Tailwind CSS

Tailwind CSS was used to build a responsive interface because it provides:

- Rapid UI development
- Utility-first styling
- Responsive layouts
- Consistent design system

---

## Eloquent Relationships

Relationships were implemented to simplify querying.

Examples include:

- LGA → Wards
- Ward → Polling Units
- Polling Unit → Results

This reduced query complexity and improved readability.

---

## Database Transactions

Question 3 uses a database transaction.

This guarantees that:

- Polling Unit is created successfully.
- All party results are saved successfully.

If any operation fails, all database changes are rolled back automatically.

---

# Challenges Encountered

While working with the supplied database, several inconsistencies were observed.

## 1. Inconsistent Ward Data

Some LGAs contained Ward records with unexpected values such as:

```
ward_id = 0
```

which affected dependent dropdown behaviour.

---

## 2. Polling Unit Mapping

Some polling units referenced ward IDs that did not perfectly align with the supplied ward table.

This appears to be part of the original dataset rather than an application issue.

---

## 3. Party Abbreviation

The supplied database contains two different representations for the Labour Party.

Example:

Party Table

```
LABOUR
```

Election Results Table

```
LABO
```

To maintain compatibility with the supplied schema, the application maps the values appropriately during insertion.

---

## 4. Polling Unit ID

The supplied dataset contains many polling units where

```
polling_unit_id = 0
```

while the primary key (`uniqueid`) remains unique.

The application therefore relies on `uniqueid`, which is the actual primary key used throughout the database.

---

# Future Improvements

If this were developed further, the following features could be added:

- Authentication
- User roles
- Search
- Pagination
- Charts
- Export to Excel
- Export to PDF
- Dashboard analytics
- Audit logs
- API endpoints
- Unit Tests
- Feature Tests

---

# Author

**Praise Taborota**

Backend / Full Stack Developer

## Tech Stack

- PHP
- Laravel
- Livewire
- JavaScript
- React
- MySQL
- Tailwind CSS

GitHub:

```
https://github.com/tabpaddy
```

LinkedIn:

```
https://www.linkedin.com/in/praiseTheDeveloper
```

Portfolio:

```
https://praisethedeveloper.vercel.app
```

---

# Assessment Status

| Requirement | Status |
|-------------|--------|
| Question 1 | Completed |
| Question 2 | Completed |
| Question 3 | Completed |

---

## License

This project was developed solely for the Bincom ICT Solutions Software Developer Technical Assessment.
