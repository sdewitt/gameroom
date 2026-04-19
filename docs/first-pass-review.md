# GameRoom First-Pass Review (April 19, 2026)

This is an initial orientation review based on the current repository and the attached phpMyAdmin SQL export.

## What I added

- `database/schema.sql`: a deduplicated, repository-friendly baseline schema (tables + views) from the SQL dump.
- Removed phpMyAdmin-specific duplication and view definers so local/dev imports are more portable.

## High-level project shape

- This is a PHP application with mostly page-oriented scripts in the repo root and an admin area under `admin/`.
- Database access appears to be direct MySQLi usage with SQL embedded in PHP scripts.
- There is no migration framework currently in use; schema appears to be managed manually.

## Database observations from the provided schema

1. **Core entities**
   - `accounts`: user profile/authentication data.
   - `gamelist`: game entries tied to owners via `ownerid`.
   - `votes`: ballot-like fields by `userid`.
   - `machineissues`: issue tracking tied to a machine/game id.
   - `tokens`: OAuth/token storage.

2. **Views in active use pattern**
   - `gl_merge`, `gl_merge3`, `Shannon_View` join `gamelist` and `accounts`.
   - `gl_merge3` and `Shannon_View` are currently hardcoded to `showyear = 2023`.

3. **Schema risks / cleanup targets (next pass)**
   - The column name ``accounts`.`2023`` is difficult to maintain and requires identifier quoting.
   - Missing explicit foreign keys in export (e.g., `gamelist.ownerid -> accounts.id`).
   - Mixed charsets/collations (`utf8mb3` and `utf8mb4`) could cause sorting/comparison drift.
   - Hardcoded year filters in views are brittle year-over-year.

## App-level observations (security + maintainability)

- DB credentials are currently committed in plaintext in `config.php` and `class-db.php`; move to environment variables/secrets immediately.
- Several scripts suggest direct request handling and inline SQL; recommend introducing a minimal data-access layer or prepared-statement audit as a follow-up.

## Suggested next steps

1. Move secrets to environment config and rotate exposed DB credentials.
2. Add SQL migrations (`database/migrations/`) and treat `database/schema.sql` as generated baseline.
3. Replace hardcoded `2023` view filters with configurable show-year logic.
4. Introduce explicit foreign keys and supporting indexes once data integrity is verified.
