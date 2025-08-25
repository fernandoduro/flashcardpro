
<div align="center">
  <img src="public/images/logo.png" alt="FlashcardPro Logo" width="150">
  <h1 style="border-bottom: none;">FlashcardPro</h1>
  <p>A modern flashcard application built with Laravel 12, Livewire, and Vue.js.</p>
</div>

---

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Author](#author)
- [Project Description](#project-description)
- [Technologies Used](#technologies-used)
- [Setup Instructions](#setup-instructions)
- [Test User Accounts](#test-user-accounts)
- [Running the Test Suite](#running-the-test-suite)
- [Architectural Decisions](#architectural-decisions)
- [Public API Overview](#public-api-overview)
- [AI Tool Usage Disclosure](#ai-tool-usage-disclosure)

---

## Author
**Name:** Fernando Henrique Caversan Santos Duro
**Email:** contato@fernandoduro.com.br

## Project Description
FlashcardPro is a full-stack web application designed to help users create, manage, and study digital flashcards. Users can organize their cards into customizable decks, engage in interactive study sessions, and track their progress over time. The application was built from the ground up without starter kits to demonstrate a deep understanding of the Laravel framework and modern web development practices.

## Technologies Used
- **PHP 8.3** / **Laravel 12.13** – Core backend framework.
- **MySQL 8** – Application database.
- **Laravel Livewire** – Primary UI for reactive, backend-driven components.
- **Vue.js** – Used for the highly interactive, client-side "Study Session" feature.
- **Tailwind CSS v4** – Utility-first CSS framework for a custom, responsive design.
- **Alpine.js** – For lightweight client-side interactivity, primarily for modals.
- **Pest PHP** – For a clean, expressive, and comprehensive test suite.
- **Laravel Sail / Docker** – For a consistent, containerized development environment.
- **Laravel Sanctum** – For robust, token-based API authentication.
- **Highcharts** – For data visualization on the statistics dashboard.

## Setup Instructions

This project is configured to run seamlessly with Laravel Sail (Docker).

**Prerequisites:**
- Docker Desktop installed and running.
- A command-line terminal (like PowerShell on Windows).

---

**1. Unzip the Project & Navigate**
Unzip the submitted `flashcard-pro.zip` file and open a terminal in the project's root directory.

**2. Prepare the Environment File**
```powershell
copy .env.example .env
```

**3. Start the Docker Containers**
The `docker-compose.yml` is configured to use the required PHP and MySQL versions.
```powershell
docker-compose up -d
```

**4. Install Composer Dependencies**
docker-compose exec laravel.test composer install

**5. Finalize Setup**
Run these commands to generate the application key, run migrations, and link the storage directory.
```powershell
docker-compose exec laravel.test php artisan key:generate
docker-compose exec laravel.test php artisan migrate:fresh --seed
docker-compose exec laravel.test php artisan storage:link
```

**6. Build Frontend Assets**
Install Node.js dependencies and start the Vite development server.
```powershell
docker-compose exec laravel.test npm install
npm run dev
```
*(Note: You will need to keep the `npm run dev` process running in its own dedicated terminal while you use the application).*

**7. Access the Application**
- **URL:** [http://localhost](http://localhost)
- The application is now running and seeded with sample data.

---

## Test User Accounts

The database seeder creates one pre-made account for immediate use:

- **Email:** `admin@example.com` / **Password:** `password`

You can also create your own account via the registration page.

---

## Running the Test Suite

The project has a comprehensive test suite written with Pest. To run all tests, execute the following command:
```powershell
docker-compose exec laravel.test php artisan test
```

---

## Architectural Decisions

- **Hybrid Frontend (Livewire + Vue.js):** The application intentionally uses a hybrid frontend to showcase proficiency in both stacks.
  - **Livewire** is used for all primary CRUD interfaces (deck and card management), demonstrating rapid, backend-driven development for forms and data-driven pages.
  - **Vue.js** is used for the "Study Session" feature, a stateful and highly interactive component that benefits from a client-side framework. This component is completely decoupled and communicates with the backend via a stateless API.

- **Stateless API with Sanctum:** A deliberate decision was made to separate the web and API authentication. The Livewire frontend uses standard session-based authentication. The API (consumed by the Vue component) is stateless and protected by Laravel Sanctum API tokens. This is a robust, modern pattern that allows the API to be used by any third-party client.

- **Manual Authentication & Breeze Retrofit:** The project was initially built with a completely manual authentication system to demonstrate foundational knowledge. It was then refactored to use Laravel Breeze, showcasing the ability to integrate and customize official starter kits into an existing application.

- **Event-Driven Token Generation:** To create a seamless UX for the hybrid stack, an event listener is attached to Laravel's `Login` event. It automatically generates a Sanctum API token and stores it in `localStorage` for the Vue component to use, completely transparently to the user.

- **Reusable Components:** UI elements like modals (`x-modal`), form components, and navigation links were built as reusable Blade components to keep the code DRY and maintain a consistent design system.

---

## Public API Overview

The FlashcardPro API is a stateless, token-based RESTful interface.

**Authentication:**
To use the API, a user must first log in via the web interface. An API token is automatically generated and stored in the browser's `localStorage`. This token must be included in the `Authorization` header of all API requests as a Bearer token:
`Authorization: Bearer <YOUR_TOKEN>`

**Documentation:**
A full, interactive Swagger/OpenAPI documentation page is available within the application at:
- **URL:** [`/api/documentation`](http://localhost/api/documentation)

---

## AI Tool Usage Disclosure

As encouraged by the challenge guidelines, an AI assistant (a large language model based on GPT-4) was used as a development partner throughout this project. It was leveraged to accelerate development, brainstorm solutions, and ensure adherence to best practices.

- **When & Where:** AI was used at all stages of the project.
  - **Initial Scaffolding:** Generating boilerplate for migrations, models, and controllers based on the provided schema.
  - **Refactoring:** Suggesting improvements to controller logic, refactoring Livewire components to use modern patterns (like computed properties), and cleaning up Blade views.
  - **Debugging:** Helping to diagnose complex issues, such as test failures, framework incompatibilities, and CSS layout problems.
  - **Code Generation:** Generating entire components or test files based on high-level descriptions, which were then reviewed, tested, and refined. For example, all commit messages were drafted collaboratively with the AI.

- **Why & How:** The goal was to simulate a modern, AI-augmented development workflow, focusing my effort on architectural decisions and problem-solving rather than boilerplate.
  - **Effective Prompting Example:** *“Refactor this Livewire component. It currently fetches data in the mount() method. Rewrite it to use computed properties for better efficiency and caching, and replace the $listeners array with modern #[On] attributes.”*

- **Accountability:** I am fully responsible for every line of code submitted. All AI-generated code was thoroughly reviewed, understood, and tested to ensure it met the project's requirements and quality standards. The AI served as a powerful productivity multiplier, but the final architecture and implementation are my own.
```
