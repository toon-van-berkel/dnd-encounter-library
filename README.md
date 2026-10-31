# D&D Encounter Library

D&D Encounter Library is a Laravel website where people can find, save and share encounters for Dungeons & Dragons.

I made this project for **CMTPRG02-5 (PRG5)** at Hogeschool Rotterdam, Creative Media & Game Technologies. I wanted to build an application where I could use what I learned about MVC, routes, databases, forms, authorization, testing and deployment.

**Live website:** https://dnd-encounter-library.alwaysdata.net  
**GitHub repository:** https://github.com/toon-van-berkel/dnd-encounter-library

## What can you do with it?

| Visitor | Logged-in user | Admin |
| --- | --- | --- |
| Browse active encounters | Register and manage account settings | Open the admin page |
| Search by title or description | Add and remove favorites | See both active and inactive encounters |
| Filter by type and difficulty | View their own encounters | Activate or deactivate encounters |
| Open encounter details | Create encounters after saving five favorites | Manage encounter visibility with a separate POST action |
| View popular encounters | Edit or delete encounters they created | Cannot automatically edit another user's encounter |

A few details I added:

- Only **active** encounters appear on public pages. An inactive encounter can still be viewed by its owner or an admin, but other users get a **404**.
- Before creating an encounter, a user must have **five favorites**. I check this with `favorites()->count()` when opening the form and again when submitting it. I don't save a separate favorites counter.
- I prevent duplicate favorites with a unique database rule on `(user_id, encounter_id)`.
- An encounter can have an optional image URL. If no image is provided, I show a placeholder. Users cannot upload image files.
- If a user deletes their account, the encounters stay in the library and the author name changes to **Deleted user**.
- I built the pages with Blade and my own CSS in `public/css/`. There is no Node.js or Vite build step.

## Technologies I used

- **Laravel 13 and PHP 8.4** for the application.
- **Laravel Fortify** for registration and login.
- **Blade** for the pages, and **Livewire 4 / Flux** for account settings.
- **Eloquent ORM**, migrations and SQLite for the database.
- **Pest** for automated tests.
- **Laravel Pint** for code formatting and **PHPStan/Larastan** for type checks.
- **GitHub Actions** to automatically run the checks when I push to `main` or open a pull request.
- **alwaysdata Free** to host the live application using PHP 8.4, HTTPS and a persistent SQLite database.

## How I used MVC

Laravel uses the **Model-View-Controller (MVC)** structure. I tried to keep each part of the application in the right place.

| Part | Where it is | What it does |
| --- | --- | --- |
| Models | `app/Models/` | Work with data and relationships, like users, encounters and favorites |
| Views | `resources/views/` | Show pages, forms and reusable Blade components |
| Controllers | `app/Http/Controllers/` | Handle requests, validation, searches and actions |
| Routes | `routes/web.php` and `routes/settings.php` | Connect URLs to the correct part of the application |
| Authorization | `app/Policies/EncounterPolicy.php` and `app/Providers/AppServiceProvider.php` | Decide who is allowed to perform certain actions |
| Database | `database/migrations/` and `database/seeders/` | Create database tables and example data |
| Tests | `tests/Feature/` | Check whether the main features work as expected |

### Database relationships

- A **User** can have many **Encounters** and **Favorites**.
- An **Encounter** belongs to a **User**, **EncounterType** and **EncounterDifficulty**, and can have many **Favorites**. The user link can be empty after the author deletes their account.
- A **Favorite** belongs to one **User** and one **Encounter**.

I use Eloquent relationships to work with these tables instead of writing all the database queries myself.

## What you need to run it

- PHP **8.4**, with the extensions Laravel needs (including `pdo_sqlite` and `sqlite3` when using SQLite).
- Composer 2.
- Git.
- Write access to `storage/` and `bootstrap/cache/`.

The current locked development packages need PHP 8.4, even though the PHP version requirement in `composer.json` is broader.

## Installing the project locally

These commands work in a terminal like Git Bash. On Windows PowerShell, you can create/copy the same files using the equivalent commands.

**1. Get the project and install the packages:**

```bash
git clone https://github.com/toon-van-berkel/dnd-encounter-library.git
cd dnd-encounter-library
composer install
cp .env.example .env
php artisan key:generate
```

**2. Create an empty SQLite file:**

```bash
touch database/database.sqlite
```

**3. Check the following settings in `.env`:**

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Keep the `APP_KEY` that Laravel generated. Don't copy a production key into your local setup.

**4. Create the database tables, add example encounters and start the server:**

```bash
php artisan migrate
php artisan db:seed --class=SampleEncounterSeeder
php artisan serve
```

The website should now be available at http://127.0.0.1:8000.

The sample seeder adds **ten encounters**: eight active and two inactive. It also creates a sample author with a random password. Running the same seeder again does not duplicate encounters that are already present.

**Note:** The default `DatabaseSeeder` also creates a test account. I use `SampleEncounterSeeder` explicitly so I don't create an unnecessary test login on my live server.

### Testing the admin role locally

New accounts get the `user` role, not `admin`. To make an existing test account an admin in my own environment, I use Laravel Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'your-test-email@example.com')->firstOrFail();
$user->role = 'admin';
$user->save();
```

An admin can open `/admin`. A normal user gets **403**. Admins can change whether encounters are active, but only the original author can edit or delete an encounter.

## Running the tests

```bash
php artisan test        # Run the Pest tests
composer lint:check     # Check formatting with Laravel Pint
composer types:check    # Run PHPStan / Larastan
composer ci:check       # Run all checks locally
```

My last recorded full test run on **30 October 2026** had **184 passing tests, one skipped test and 558 assertions**. Pint passed, and PHPStan reported zero errors.

I also fixed a GitHub Actions configuration issue, and the workflow passed on commit [`0ef9c2b`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/0ef9c2b15c5e88106adddaa5870d83437a20ab6a). GitHub Actions uses PHP 8.4 and an in-memory SQLite database, so it doesn't need access to the live database.

## Security and deployment

I wrote two separate documents about security and the different environments:

- [OWASP and security](docs/OWASP.md)
- [OTAP and deployment](docs/OTAP.md)
- [Development history](CHANGELOG.md)

For the live application, I keep `APP_DEBUG=false`, use a separate `APP_KEY`, and only expose Laravel's `public/` directory. The SQLite database and `.env` are not inside the public website folder.

The live website currently uses `MAIL_MAILER=log`, so password-reset emails are not actually delivered. I also deploy changes **manually over SSH**; GitHub Actions checks the code but does not update alwaysdata for me.

## About the project

I made this application as an individual PRG5 assignment. My Git history shows how I built it, and I can demonstrate the features, tests, code and live website during the oral assessment.
