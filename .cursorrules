# Project Rules & Guidelines

This document outlines the core architecture, technology stack, and development guidelines for the **Laravel Quiz System**. Always refer to these rules when contributing to or modifying the project to ensure consistency.

## 1. Project Overview
This is a comprehensive quiz application where users can participate in quizzes, view their results, and compete on leaderboards. The project supports both guest and registered users, alongside an admin panel for content management.

### Key Features
* **Admin Module:** Manage admins, quizzes, questions, options, and view all tests taken.
* **User Module:** Authentication, quiz participation (registered/guest), personal results, and leaderboards.

## 2. Technology Stack
* **Framework:** Laravel 13
* **Language:** PHP 8.1+
* **Frontend:** Livewire & Alpine.js
* **Styling:** Tailwind CSS
* **Build Tool:** Vite
* **Database:** Relational Database (MySQL) using **Eloquent ORM**

## 3. Core Database Entities
* **User:** Handles authentication (Regular users and Admins).
* **Quiz:** A collection of questions.
* **Category:** Used to group questions.
* **Question:** Belongs to Categories and Quizzes.
* **Option:** Possible answers for a Question.
* **Test:** Represents a user taking a quiz.
* **Answer:** Represents a user's chosen option for a specific test question.

## 4. Development Guidelines

### Coding Standards
* Follow **PSR-12** coding standards.
* Use strict typing where possible in PHP.
* Keep controllers thin and move complex business logic into services, actions, or directly handle them within Livewire components when appropriate.

### Frontend & UI
* Use **Tailwind CSS** utility classes for styling. Avoid writing custom CSS unless absolutely necessary.
* Use **Livewire** and **Alpine.js** for dynamic interfaces (e.g., leaderboards, quiz taking forms). Do not rely on vanilla JavaScript/jQuery unless integrating a specific library that Livewire and Alpine cannot handle.

### Eloquent & Database
* Always use strong types in migrations.
* Use route model binding where possible.
* Define relationships explicitly (e.g., `hasMany`, `belongsTo`, `belongsToMany`).
* Prevent N+1 queries by using `with()` for eager loading when fetching relations (e.g., fetching a quiz with its questions and options).

### Routing
* STRICT RULE: **Never write business logic or database queries inside routing files** (`web.php` or `api.php`). Route closures are strictly forbidden for data fetching. Always route to a generic Controller or Single-Action Controller.
* Group routes by middleware and common prefixes (e.g., `admin` prefix for admin routes).
* Use resource routes where possible instead of defining each CRUD route manually.

### Testing
* Run `php artisan test` before submitting changes.
* Ensure feature tests cover any new Livewire components or API endpoints created.

## 5. Branching & Deployment
* Never push directly to the `main` or `master` branch.
* Ensure the `.env` configuration is properly mirrored in `.env.example` when adding new services or API keys.
