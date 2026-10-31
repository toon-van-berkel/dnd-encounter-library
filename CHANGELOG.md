# Changelog — D&D Encounter Library

This is my development log for PRG5. I used my **actual Git commits and their dates** to show how I built the project. I grouped some commits together so it is easier to follow what I worked on.

I also included the deployment and manual tests at the end. Those are things I did on the server, so they do not always have their own Git commit.

## 7 October 2026 — Starting the project

- I created the Laravel project and set up the Git repository.
- Commit: [`5233f72`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/5233f72da4727a15c9e4698d082624643b0f1aad).

## 13 October 2026 — MVC and database structure

- I started building `EncounterController` and the encounter pages.
- I created a reusable layout and navigation with Blade and started writing my own CSS.
- I made my database migrations based on my ERD.
- I added the first Eloquent models and relationships for encounters, types and difficulties.
- I replaced hard-coded example content with data from the database.

Example commits: [`de2ef77`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/de2ef77803986288253cfcc9dfffeaa0cf84fa1b), [`b99cdc5`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/b99cdc568074729577bbbdd393fb49ab08c26d6e), [`d1f3075`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/d1f307588b7a438d0f54fee100a0f77cf8ef4f60).

## 14 October 2026 — Encounter forms

- I built the form for creating encounters and connected it to the controller.
- I improved the relationships between my models.
- I added example types and difficulties to the database seeder.
- I worked on the styling of forms and encounter cards.

Example commits: [`d97b384`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/d97b3845ecc4425c58fcb6b567f6b0bd433474bb), [`9e29a6d`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/9e29a6d0b33b3b98b7176313d630817cfd874937), [`063f050`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/063f0500fa70e0f7dbf7bcb888003644c265f839).

## 26 October 2026 — Login, security and tests

- I made sure actions like creating and editing encounters require a logged-in user.
- I added `EncounterPolicy` so users can only edit or delete their **own** encounters, even if they try to change the URL manually.
- I fixed and adjusted the authentication pages and account settings.
- I wrote tests for invalid encounter input and set up my PHP formatting rules.

Example commits: [`77377b4`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/77377b4c16b404f1e2f1c59cfc2a512ea7dd1104), [`f2212e2`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/f2212e26f2c419892ed6ee10e08a2d7b94516511), [`18331a9`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/18331a9458871f40bd81a7fb9b87690dce2841d3).

## 27 October 2026 — Encounter features and layout

- I finished the main encounter pages: listing, details, creating, editing and deleting.
- I added server-side validation for the form fields.
- I improved the navigation, breadcrumbs, homepage and styling.
- I adjusted the login redirects and views so they fit the application better.

Example commits: [`2f5bf52`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/2f5bf5246f3bb5b577ae828abcf0d1be2e205383), [`74250e2`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/74250e290f23487a473bcb367cccdcd1f3bcf87f), [`d2b94fa`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/d2b94fa5f267ee8b6108ddd54c4f070e1f840f11).

## 28 October 2026 — Admin and personal dashboard

- I made a dashboard where users can see the encounters they created.
- I added an admin area for managing encounters.
- I made a separate POST action to activate or deactivate encounters.
- I improved the admin table and the encounter design.

Commits: [`6c6015e`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/6c6015ee8a987369cb68db3a57045587661308fd), [`908849c`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/908849cf60d2d9b54e6401e47a1838070d31d9b5).

## 29 October 2026 — Search, filters and favorites

- I added searching by encounter title and description.
- I added dropdown filters for type and difficulty.
- I made the favorites feature and added the rule that users need five favorites before creating an encounter.
- I used a separate Eloquent count query for that rule instead of storing a counter.
- I created a sample seeder to make it easier to demonstrate the project.

Commits: [`14afdb9`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/14afdb9e0f1a4ed72a64762c88ddeb4827d69fa7), [`5498bf7`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/5498bf704be43ac17def9ae780fbc85a5c6c06f9).

## 30 October 2026 — Final improvements and GitHub Actions

- I finished some final features, including the homepage and handling encounters when an account is deleted.
- I organized my Pest tests into folders and added more tests.
- I finished the UI components and removed starter-kit files I wasn't using.
- I removed the Vite/Node build setup because the website uses my own CSS.
- I applied Laravel Pint formatting and fixed the remaining PHPStan issues.
- I had a GitHub Actions error because `phpunit.xml` still mentioned an empty `tests/Unit` folder. That folder was not included in Git. I fixed the configuration and split the checks into separate steps, so I could see more easily where a problem happened.

Commits: [`b343240`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/b343240f8bafe9f8e3dc389e1dfb78fc3ac37a0d), [`f80b8e5`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/f80b8e535448b554e8b3dba7d68e9e177d12e4d1), [`080e28f`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/080e28fbbbde106c3d7b1cf3490ddf478abc3b23), [`a8f797b`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/a8f797bf75cf807f7ea8dbe6b4185d300e7e5b8b), [`088996f`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/088996fc727f0563e7b83d6ff3ed61d0224284f4), [`0ef9c2b`](https://github.com/toon-van-berkel/dnd-encounter-library/commit/0ef9c2b15c5e88106adddaa5870d83437a20ab6a).

### Tests and CI on 30 October

- My last recorded local check passed with **184 passing tests, one skipped test and 558 assertions**.
- Laravel Pint passed, and PHPStan reported no errors.
- After I fixed the configuration, GitHub Actions also passed.

## Deployment and final checks

These were practical steps I performed outside of Git commits.

### 30 October 2026 — First live deployment

- I created a free account on alwaysdata and connected to the server using SSH.
- I checked PHP 8.4, Composer and Git, cloned the repository, and installed the production dependencies.
- I configured a separate `.env`, generated an application key and created a persistent SQLite database.
- I ran all **nine migrations** and added the ten example encounters using `SampleEncounterSeeder` (eight active, two inactive).
- I set the website root to Laravel's `public/` folder and opened the site over HTTPS.

### 31 October 2026 — Testing the live application

- I tested registration, login, logout and public pages.
- I saved five favorites, created an encounter and then edited and viewed it.
- I opened the admin page using my admin account.
- I tested `/admin` with a normal account and got **HTTP 403**, which is the correct result.
- I am tracking the remaining final checks and submission ZIP in [issue #25](https://github.com/toon-van-berkel/dnd-encounter-library/issues/25). I will only mark the issue complete when I have finished those steps.
