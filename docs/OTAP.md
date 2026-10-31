# OTAP and deployment — D&D Encounter Library

## How I use OTAP in my project

OTAP stands for **Ontwikkeling, Test, Acceptatie en Productie** (Development, Testing, Acceptance and Production). The idea is to keep the environment where I write code separate from the environment where people use the finished application.

For my PRG5 project, I have **three actual environments**: my own computer, GitHub Actions and alwaysdata. I do the acceptance tests manually on the live website. I do not have a separate acceptance server.

| Phase | Where I do it | What I use it for |
| --- | --- | --- |
| **O — Development** | My Windows computer | Writing code, using my local `.env` and SQLite database, and trying out features |
| **T — Testing** | GitHub Actions (`ubuntu-latest`) | Running Laravel Pint, PHPStan and Pest automatically, with SQLite `:memory:` |
| **A — Acceptance** | Manual testing on the deployed website | Checking whether the main features work for guests, users and admins |
| **P — Production** | alwaysdata Free | Running the public Laravel application with PHP 8.4, HTTPS and a separate persistent SQLite database |

For a larger project, I would use a separate acceptance environment. That would let me test changes without touching live data.

## Different settings for each environment

Laravel uses the `.env` file for settings that can be different per environment. I do not put my production `.env` in GitHub.

| Setting | Local development | GitHub Actions | Production |
| --- | --- | --- | --- |
| `APP_ENV` | `local` | `testing` | `production` |
| `APP_DEBUG` | `true` | `false` | `false` |
| `APP_URL` | Local website address | No public website | `https://dnd-encounter-library.alwaysdata.net` |
| Database | Local SQLite file | SQLite `:memory:` | Separate SQLite file on the server |
| `APP_KEY` | Generated locally | Temporary key per run | Separate key generated on the server |
| Sessions and cache | Local settings | `array` | `file` |
| Queues | Local settings | `sync` | `sync` |
| Mail | Local settings | `array` | `log` |

I only document the settings and their purpose, not actual passwords or secret keys. `APP_DEBUG=false` is especially important on production because visitors should not see detailed error information.

## Testing with GitHub Actions

My workflow is in [`.github/workflows/tests.yml`](../.github/workflows/tests.yml).

Whenever I push to `main` or open a pull request, GitHub Actions automatically:

1. Downloads the repository.
2. Sets up PHP 8.4 and the SQLite extensions.
3. Creates a temporary `.env` and installs the Composer dependencies.
4. Generates an application key for testing.
5. Runs **Laravel Pint** to check formatting.
6. Runs **PHPStan/Larastan** to check for possible code problems (level 7, 512 MB memory limit).
7. Runs the **Pest tests** using SQLite in-memory and Laravel's `RefreshDatabase`.

I do not need Node.js or Vite in this workflow because my website uses regular CSS files in `public/css/`. GitHub Actions does not use my production database and does not deploy the website automatically.

### A problem I ran into

On 30 October, GitHub Actions failed even though my local tests were passing. The problem was in `phpunit.xml`: it still pointed to `tests/Unit`, but that folder was empty. Git does not save empty folders, so it did not exist on GitHub's runner.

I removed that unused test suite and made the GitHub Actions steps easier to read. After I pushed the fix, the workflow passed on commit [`0ef9c2b`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/0ef9c2b15c5e88106adddaa5870d83437a20ab6a).

My last recorded full test check had **184 passing tests, one skipped test and 558 assertions**. Pint passed, and PHPStan reported no errors.

## My production server: alwaysdata

I chose **alwaysdata Free** because I wanted to put the application online without paying for hosting. The free plan is enough for a small school demonstration, although it has limited resources.

**Live website:** https://dnd-encounter-library.alwaysdata.net  
**Hosting:** alwaysdata Free  
**PHP:** 8.4 (8.4.27 when I first deployed)  
**Application folder:** `/home/dnd-encounter-library/apps/dnd-encounter-library`  
**Public website folder:** `/home/dnd-encounter-library/apps/dnd-encounter-library/public`  
**Database:** SQLite, saved in the application's `database/` folder outside the public webroot

### How I deployed the application (30 October 2026)

First, I created the hosting account and checked that SSH, PHP, Composer and Git worked. I then connected to the server using SSH and cloned my existing repository:

```bash
mkdir -p ~/apps
git clone --depth 1 https://github.com/toon-van-berkel/dnd-encounter-library.git \
  ~/apps/dnd-encounter-library
cd ~/apps/dnd-encounter-library
```

I installed only the packages needed to run the application in production:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader \
  --no-interaction --no-progress
```

After that, I copied `.env.example` to `.env`, set `APP_ENV=production` and `APP_DEBUG=false`, configured SQLite and generated a new application key:

```bash
php artisan key:generate --force --no-interaction
```

I created the SQLite database file and made sure Laravel could write to `storage/` and `bootstrap/cache/`. Then I ran:

```bash
php artisan config:clear
php artisan migrate --force
php artisan migrate:status
php artisan db:seed --class=SampleEncounterSeeder --force
```

All **nine migrations** completed successfully. The sample seeder added **eight active and two inactive encounters**. I chose this specific seeder because running the default `DatabaseSeeder` would also create a test user.

Finally, I changed the site's root directory in alwaysdata to Laravel's `public/` folder. This is important because the `.env`, database and application code should not be directly accessible from a browser. I then opened the website over HTTPS and checked that it worked.

## How I would update the website

**Deployment is still manual.** A push to GitHub does not automatically update alwaysdata.

For a future update, my process would be:

1. Finish the changes locally and run my tests.
2. Commit and push the code.
3. Check that GitHub Actions is green.
4. Back up my production SQLite database and `.env` somewhere safe, outside the public website folder.
5. Connect to alwaysdata using SSH and check `git status` before updating.
6. Update the server to the tested commit and install production dependencies if needed.
7. Run any new migrations using `php artisan migrate --force` and clear/update caches where needed.
8. Open the live website and check that the main features still work.

This is the process I plan to use for later updates. I have **not** built an automatic deployment pipeline or a separate staging environment.

## What I tested on production

I manually checked the following on the live website:

- The homepage and the public list of encounters.
- Registering, logging in and logging out.
- Saving five favorites and then creating an encounter.
- Editing and viewing my own encounter.
- Opening the admin page with an admin account.
- Opening `/admin` with a regular account. Laravel correctly returned **403** on 31 October 2026.

Before the final submission, I still need to make sure any remaining acceptance checks are completed. These include combinations of search and filters, invalid form input, admin status changes, editing someone else's encounter via a direct URL, and checking the final ZIP. I should only mark a test as complete once I have actually performed it.

## Important production settings

- **Website root:** I only expose the `public/` folder.
- **Secrets:** I keep `.env`, passwords and `APP_KEY` out of GitHub and the submission ZIP.
- **Database:** The SQLite file is outside `public/`, and I need a backup before changing the database structure.
- **HTTPS:** I use HTTPS for the live website.
- **Debugging:** I keep `APP_DEBUG=false` in production.
- **Email:** `MAIL_MAILER=log` means password-reset emails are not sent to real inboxes yet.
- **Git:** I still need to make sure SQLite files are ignored using `/database/*.sqlite*` in `.gitignore` and that no database is tracked.
- **Hosting limits:** The free hosting plan is fine for my school project but is not intended for a busy production service.

## What I learned from OTAP

The main thing I learned is that my local project, automated tests and live website each need their own settings and database. I also learned why it matters to use environment variables, keep secrets private and test migrations before updating a live application.

Even though I do not have a separate acceptance server or automatic deployment, I can explain how I moved my existing Laravel project to an external server and how GitHub Actions helps me catch problems before deploying.

## References

- PRG5 Lesson 8: OTAP and deployment (course material)
- Laravel configuration: https://laravel.com/docs/13.x/configuration
- Laravel migrations: https://laravel.com/docs/13.x/migrations
- Laravel deployment: https://laravel.com/docs/13.x/deployment
- GitHub Actions: https://docs.github.com/en/actions
