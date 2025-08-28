
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
- [Environment Variables](#environment-variables)
  - [Required](#required)
  - [Database](#database)
  - [AI Services (Optional)](#ai-services-optional)
  - [Docker environment(Optional)](#docker-environmentoptional)
- [Test User Accounts](#test-user-accounts)
- [Running the Test Suite](#running-the-test-suite)
- [Architectural Decisions](#architectural-decisions)
- [Public API Overview](#public-api-overview)
- [Troubleshooting](#troubleshooting)
  - [Quick Start Checklist](#quick-start-checklist)
  - [Common Issues \& Solutions](#common-issues--solutions)
    - [1. **Test Failures**](#1-test-failures)
    - [2. **Vue.js Study Session Not Loading**](#2-vuejs-study-session-not-loading)
    - [3. **AI Card Generation Not Working**](#3-ai-card-generation-not-working)
    - [4. **Database Connection Issues**](#4-database-connection-issues)
    - [5. **Asset Compilation Issues**](#5-asset-compilation-issues)
    - [6. **Docker Permission Errors**](#6-docker-permission-errors)
    - [7. **Stale Cache Errors**](#7-stale-cache-errors)
    - [8. **Performance Issues**](#8-performance-issues)
    - [9. **Environment Configuration Problems**](#9-environment-configuration-problems)
  - [Getting Help](#getting-help)
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
Unzip the submitted `flashcardpro.zip` file and open a terminal in the project's root directory.

**2. Prepare the Environment File**
```bash
cp .env.example .env
```

**4. Install Composer Dependencies**

```bash
composer install
```

**3. Start the Docker Containers**
The `docker-compose.yml` is configured to use the required PHP and MySQL versions. You should have 3 containers running (`flashcardpro`,`mysql` and `selenium`).

```bash
sail up -d
```

**5. Finalize Setup**
Run these commands to generate the application key, run migrations, and link the storage directory.

```bash
sail artisan key:generate
sail artisan migrate:fresh --seed
sail artisan storage:link
```

**6. Build Frontend Assets**
Install Node.js dependencies and start the Vite development server.

```bash
sail npm install
sail npm run dev
```

*(Note: You will need to keep the `npm run dev` process running in its own dedicated terminal while you use the application).*

**7. Access the Application**
- **URL:** [http://localhost](http://localhost)
- The application is now running and seeded with sample data.

---

## Environment Variables

The application requires the following environment variables:

### Required
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local, production, etc.)
- `APP_KEY` - Laravel application key
- `APP_DEBUG` - Debug mode (false in production)
- `APP_URL` - Base application URL
- `TELESCOPE_ENABLED` - Enable Laravel Telescope (false in production)

### Database
- `DB_CONNECTION` - Database driver (mysql, sqlite, etc.)
- `DB_HOST` - Database host
- `DB_PORT` - Database port
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

### AI Services (Optional)

- `GEMINI_API_KEY` - (Recommended) Google Gemini API key for AI card generation ([How to get one](https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=&cad=rja&uact=8&ved=2ahUKEwjCx9vf8auPAxUFr5UCHWiYLpEQ-tANegQIIxAC&url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DRVGbLSVFtIk%26t%3D21&usg=AOvVaw2aA_gMq6SXtJwBfDsUOOrr&opi=89978449)
- `OPENAI_API_KEY` - OpenAI API key (experimental alternative) ([Where do I find one?](https://help.openai.com/en/articles/4936850-where-do-i-find-my-openai-api-key)
- `AI_MAIN_ENGINE` - Preferred AI engine (gemini or openai)

### Docker environment(Optional)
- `WWWGROUP` - 1000 for it to work on Windows 
- `WWWUSER` - 1000 for it to work on Windows 
- `FORWARD_DB_PORT` - 3309 or any other if host machine is occupied

---

## Test User Accounts

The database seeder creates one pre-made account for immediate use and testing:

- **Email:** `admin@example.com` / **Password:** `password`

You can also create your own account via the registration page.

---

## Running the Test Suite

The project has a comprehensive test suite. To run all tests, execute the following commands:

#Pest tests 
```bash
cp .env.testing.example .env.testing
sail artisan key:generate --env=testing
sail test
```

#Laravel Dusk
```bash
cp .env.dusk.local.example .env.dusk.local
sail artisan key:generate --env=dusk.local
sail npm run build
sail run touch /var/www/html/database/dusk.sqlite
sail run chown -R sail:sail /var/www/html/database/dusk.sqlite
sail dusk
```
*(Note: Drop your `npm run dev` process, or it will overwrite built JS and CSS files, breaking the test. Dusk will also temporarily replace your .env with the .env.dusk.local).*

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

## Troubleshooting

### Quick Start Checklist

Before diving into specific issues, verify these essentials:

- ✅ Docker Desktop is running
- ✅ No other services are using ports 80, 3306, or 5173
- ✅ `.env` file exists and contains required variables
- ✅ Database is accessible and migrated
- ✅ Frontend assets are compiled (`npm run dev` is running)

### Common Issues & Solutions

#### 1. **Test Failures**

**Symptoms:**
- Tests failing with 404 errors on API endpoints
- `php artisan test` shows API route not found errors

**Solutions:**
```bash
# Clear all Laravel caches
sail artisan optimize:clear

# Restart containers to ensure fresh state
sail down
sail up -d

# Regenerate autoloader
sail composer dump-autoload

# If still failing, check routes
sail artisan route:list --path=api
```

**Prevention:** Always run `php artisan optimize:clear` after making routing changes.

#### 2. **Vue.js Study Session Not Loading**

**Symptoms:**
- Study session page shows blank or error message
- Console shows "API token not found" error
- JavaScript errors in browser developer tools

**Solutions:**
```bash
# 1. Verify API token generation
sail tinker
# In tinker:
$user = App\Models\User::first();
$user->tokens()->delete(); // Clear old tokens
$user->createToken('test')->plainTextToken;

# 2. Check if Vite dev server is running
sail run ps aux | grep node

# 3. Rebuild assets if needed
sail npm run build

# 4. Clear browser cache and localStorage
# Open browser dev tools → Application → Local Storage → Clear
```

**Prevention:** Ensure `npm run dev` is running and API token is generated after login.

#### 3. **AI Card Generation Not Working**

**Symptoms:**
- "Failed to generate cards" error message
- AI generator returns null or empty results
- API key errors in logs

**Solutions:**
```bash
# 1. Check API key configuration
sail tinker
# In tinker:
dd(env('GEMINI_API_KEY')); // Should not be null

# 2. Verify AI engine setting
dd(env('AI_MAIN_ENGINE', 'gemini')); // Should be 'gemini' or 'openai'

# 3. Test AI service connectivity
# For Gemini:
curl -H "x-goog-api-key: YOUR_API_KEY" \
     "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?alt=json" \
     -d '{"contents":[{"parts":[{"text":"Hello"}]}]}'

# 4. Check application logs
sail run tail -f storage/logs/laravel.log
```

**Prevention:** Always set either `GEMINI_API_KEY` or `OPENAI_API_KEY` in your `.env` file.

#### 4. **Database Connection Issues**

**Symptoms:**
- `SQLSTATE[HY000]` or connection refused errors
- Migrations fail to run
- Application shows database connection errors

**Solutions:**
```bash
# 1. Check database service status
sail run ps mysql

# 2. Verify database credentials in .env
cat .env | grep DB_

# 3. Test database connection
sail tinker
# In tinker:
DB::connection()->getPdo();

# 4. Reset database if needed
sail artisan migrate:fresh --seed

# 5. Check MySQL logs
sail logs mysql
```

**Prevention:** Ensure Docker has sufficient resources and no port conflicts.

#### 5. **Asset Compilation Issues**

**Symptoms:**
- CSS/JS files not loading or showing errors
- Vite dev server not starting
- Hot reload not working

**Solutions:**
```bash
# 1. Install dependencies
sail npm install

# 2. Start Vite dev server
npm run dev

# 3. Build for production
sail npm run build

# 4. Clear Vite cache
rm -rf node_modules/.vite

# 5. Check for port conflicts
netstat -tulpn | grep 5173
```

**Prevention:** Keep `npm run dev` running in a dedicated terminal during development.

#### 6. **Docker Permission Errors**

**Symptoms:**
- `Permission denied` errors
- Cannot write to storage or bootstrap/cache directories
- File ownership issues

**Solutions:**
```bash
# 1. Fix ownership (run as root)
docker-compose exec -u root flashcardpro chown -R sail:sail /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Fix permissions
sail run chmod -R 755 storage
sail run chmod -R 755 bootstrap/cache

# 3. Restart containers
sail down
sail up -d
```

**Prevention:** Configure proper user permissions in docker-compose.yml for Windows development.

#### 7. **Stale Cache Errors**

**Symptoms:**
- `ClassNotFoundException` after creating new classes
- `RouteNotFoundException` after adding routes
- Changes not reflected after code modifications

**Solutions:**
```bash
# Clear all Laravel caches
sail artisan optimize:clear

# Clear specific caches
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear

# Regenerate autoloader
sail composer dump-autoload

# Restart PHP-FPM if using production
sail artisan optimize
```

**Prevention:** Run `php artisan optimize:clear` after any structural changes.

#### 8. **Performance Issues**

**Symptoms:**
- Slow page loads
- High memory usage
- Database query timeouts

**Solutions:**
```bash
# 1. Check slow queries
sail tinker
# In tinker:
DB::listen(function ($query) {
    if ($query->time > 1000) { // Log queries slower than 1s
        Log::info('Slow Query', [
            'sql' => $query->sql,
            'time' => $query->time,
            'bindings' => $query->bindings
        ]);
    }
});

# 2. Enable query logging
# Add to .env:
DB_LOG_QUERIES=true

# 3. Check memory usage
sail php -r "echo 'Memory: ' . memory_get_peak_usage(true)/1024/1024 . ' MB' . PHP_EOL;"

# 4. Optimize images and assets
sail artisan storage:link
```

**Prevention:** Use computed property caching and eager loading as implemented in the Statistics component.

#### 9. **Environment Configuration Problems**

**Symptoms:**
- Application not loading with correct settings
- Environment variables not being read
- Configuration cache issues

**Solutions:**
```bash
# 1. Verify .env file
cat .env | grep APP_ENV
cat .env | grep APP_DEBUG

# 2. Clear configuration cache
sail artisan config:clear
sail artisan config:cache

# 3. Check cached configuration
sail artisan config:show app

# 4. Regenerate application key
sail artisan key:generate
```

**Prevention:** Use `php artisan config:cache` in production but `php artisan config:clear` during development.

### Getting Help

If you encounter an issue not covered here:

1. **Check the logs:**
   ```bash
   sail run tail -f storage/logs/laravel.log
   sail logs flashcardpro
   ```

2. **Verify your environment:**
   ```bash
   sail artisan about
   sail composer show
   ```

3. **Test API endpoints:**
   ```bash
   # Get API documentation
   curl http://localhost/api/documentation
   ```

4. **Common debugging commands:**
   ```bash
   # Full system reset
   sail down -v
   sail up -d
   composer install
   sail artisan migrate:fresh --seed
   sail npm install && npm run dev
   ```

**Remember:** Most issues can be resolved by clearing caches and restarting services. Always check logs first when debugging!

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
