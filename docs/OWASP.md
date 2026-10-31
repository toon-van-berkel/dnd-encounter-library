# OWASP and security — D&D Encounter Library

## Why I looked at OWASP

For PRG5, I looked at security risks that could affect my application. I used the **OWASP Top 10:2025** as a guide and checked which risks are relevant to my code.

Laravel already handles part of the security, but I still need to use its features correctly. Below I explain what the risks are, what I did to reduce them, and what I actually tested. This is not a full security audit.

## A01:2025 — Broken Access Control

**What could go wrong?** A user could try to edit someone else's encounter or open an admin page by typing the URL directly. Simply hiding an edit button would not be enough to stop this.

**What I did:**

- In `routes/web.php`, I use `auth` middleware for actions that require a logged-in user, like creating encounters and managing favorites.
- I protect the admin routes with `['auth', 'can:access-admin']`. The `access-admin` Gate is defined in `app/Providers/AppServiceProvider.php` and checks the user's role through `User::isAdmin()`.
- I use `app/Policies/EncounterPolicy.php` to check whether someone owns an encounter. Only the owner can edit or delete it.
- In `EncounterController`, the `edit()`, `update()` and `destroy()` methods use `Gate::authorize()`. This checks permission on the server, even if someone enters a URL manually.
- Public pages only list active encounters. If an encounter is inactive, its detail page returns **404** for visitors and regular users who do not own it. The owner and an admin can still view it.

**How I tested it:** On 31 October 2026, I opened `/admin` with a normal account on my live website. Laravel returned **403 — This action is unauthorized**. That is the result I expected.

One important detail: an admin can activate or deactivate an encounter, but cannot automatically edit or delete another user's encounter. I kept those permissions separate.

## A05:2025 — Injection (SQL injection and XSS)

**What could go wrong?** Someone could enter harmful text in a form or search field. If the application handles that input incorrectly, it might affect the database or run unwanted code in someone's browser.

**What I did:**

- I use Eloquent's query builder for searching. In `EncounterController::index()`, the search uses `where()` and `orWhere()` instead of building SQL strings manually. Laravel binds the entered values to the query.
- I validate submitted data with `$request->validate()` in `EncounterController::store()` and `update()`. For example, the title is required, levels must be between 1 and 20, the maximum level cannot be lower than the minimum, and type/difficulty IDs must exist.
- I display titles and descriptions with Blade's `{{ ... }}` syntax. Blade escapes this text, so it is not treated as HTML code.
- I also use HTML form validation to help users, but the real checks happen on the server. Browser-side checks can be bypassed.

**What could still be improved:** Encounters can contain an external image URL. I currently check whether it looks like a valid URL, but I do not restrict which websites it can point to. For a larger application, I would consider limiting image sources or adding a Content Security Policy (CSP).

## A07:2025 — Authentication Failures

**What could go wrong?** Someone could try to get into another person's account, for example through weak passwords or incorrectly protected pages.

**What I did:**

- I use **Laravel Fortify** for registration, login and password-reset functionality instead of building authentication from scratch.
- In `app/Models/User.php`, the password uses Laravel's `hashed` cast, so passwords are not stored as plain text.
- I use Laravel's password validation rules. In production, `AppServiceProvider` requires a password of at least 12 characters, with uppercase and lowercase letters, numbers and symbols. Laravel also checks whether the password is known to be compromised.
- Routes for personal pages and settings require authentication.
- New accounts get the `user` role by default. An admin role cannot be selected during registration; I assign that role separately.

**Current limitation:** Production uses `MAIL_MAILER=log`. That means password-reset emails are logged instead of delivered. The password-reset feature is therefore not fully usable on the live website until I configure a real mail service.

## A02:2025 — Security Misconfiguration

**What could go wrong?** Incorrect server settings could expose error details, passwords, configuration files or even the database.

**What I did:**

- I configured alwaysdata with `APP_ENV=production` and `APP_DEBUG=false`, so Laravel does not show detailed debug information to visitors.
- I generated a separate production `APP_KEY` and keep the `.env` file out of Git.
- I pointed the website to Laravel's `public/` directory, **not** to the root of the project. This keeps `.env`, `vendor/`, the database and application code outside the public website folder.
- I gave the `.env` file and SQLite database restricted file permissions.
- I use HTTPS on the live website and a lower logging level in production.

**One check I still need to complete:** The version of `.gitignore` I checked does not explicitly ignore SQLite database files. Before submission, I need to add `/database/*.sqlite*` and check with `git ls-files` that no database is tracked. I also need to make sure the ZIP does not contain `.env`, secrets or a local database. Adding a `.gitignore` rule will not remove something already committed.

## Other security measures I used

### CSRF protection

I use `@csrf` in Blade forms that send POST requests, including favorites, creating encounters and changing encounter status as an admin. For editing and deleting, I also use `@method('PUT')` or `@method('DELETE')`. Laravel checks the CSRF token to help stop unwanted requests made from another website.

The admin's activate/deactivate button uses a **separate POST action**, as required for PRG5.

### The five-favorites rule

Before someone can create an encounter, they must save five favorites. I check this using `favorites()->count()` in both `EncounterController::create()` and `store()`.

I deliberately check it again when the form is submitted. Otherwise, someone could skip the page and send a POST request directly. I do not store the number of favorites in a separate database column. The favorites table also prevents the same user from adding the same encounter twice.

### Deleting an account

When someone deletes their account, I keep their encounters but remove the link to that user. The migration `2026_10_30_123315_make_encounter_owner_nullable.php` uses `nullOnDelete()`, and the website then displays **Deleted user**. Personal favorites are removed when the user is deleted.

In `DeleteUserForm`, the user must enter their current password before deleting the account.

### Dependencies and automated checks

I use `composer.lock` so the same dependency versions can be installed again. GitHub Actions automatically runs Laravel Pint, PHPStan/Larastan and Pest tests. The workflow actions are also pinned to specific commits.

These checks help me catch mistakes, but they do **not** replace a security scan or a penetration test.

## What I have checked

| Check | Result |
| --- | --- |
| Normal user opening `/admin` on production | **403**, tested on 31 October 2026 |
| Saving five favorites and creating an encounter | Worked during my manual production test |
| Only owners can edit/delete their encounters | Protected by `EncounterPolicy` and covered by feature tests |
| Form input validation | Implemented in `EncounterController` and covered by tests |
| GitHub Actions | Passed on commit [`0ef9c2b`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/0ef9c2b15c5e88106adddaa5870d83437a20ab6a) |
| Production debug settings | Configured with `APP_DEBUG=false` |

## Things I could improve later

The application is intended as a school project, not as a high-traffic public service. If I continued developing it, I would improve backups and monitoring, configure real email delivery, and make the external image URLs more restrictive. I would also keep checking dependencies for security updates.

These are the security risks I focused on. I am not claiming that the application is protected against every possible OWASP risk.

## References

- OWASP Top 10:2025: https://top10.owasp.org/2025/
- Laravel authorization: https://laravel.com/docs/13.x/authorization
- Laravel validation: https://laravel.com/docs/13.x/validation
- Laravel CSRF protection: https://laravel.com/docs/13.x/csrf
- Laravel deployment: https://laravel.com/docs/13.x/deployment
- PRG5 Lesson 7: Security and OWASP (course material)
