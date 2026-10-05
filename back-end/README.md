# Back-end (Laravel)

A Laravel 12 app that serves the store's GraphQL API at `POST /graphql`, using
[Lighthouse](https://lighthouse-php.com/). The schema matches the original
plain-PHP back-end, so the React front-end works without any changes.

## Layout

| Path | Purpose |
| --- | --- |
| `graphql/schema.graphql` | GraphQL schema (queries `getCategories`, `getProducts`, `getProductById`; mutation `createOrder`) |
| `app/GraphQL/Queries/` | Resolvers for the product queries |
| `app/Models/` | Eloquent models. `Tech` and `Clothes` extend `Product` and are limited to their own category |
| `database/migrations/` | Table definitions (replace the old `DBsetup.php`) |
| `database/seeders/CatalogSeeder.php` | Loads `database/data/data.json` (replaces the old `Seeder.php`). It does nothing if data is already there |
| `tests/Feature/GraphQLApiTest.php` | API tests that run on in-memory SQLite |

## Local setup

1. `composer install`
2. `cp .env.example .env` and `php artisan key:generate`
3. Set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` for your MySQL server.
   For TLS (for example on Aiven), set `MYSQL_ATTR_SSL_CA` to the path of the CA certificate.
4. `php artisan migrate --seed`
5. `php artisan serve --port=8000`. The front-end's default `REACT_APP_API_URL`
   (`http://localhost:8000/graphql`) points here.

Run the tests with `php artisan test`.

## Deployment

The root `Dockerfile` copies Render's secret files (`/etc/secrets/.env` and `ca.pem`) into place.
It then caches the config, runs the migrations and seeder, and serves the app on `$PORT`.
The secret `.env` must use Laravel's variable names: `APP_KEY`, `DB_DATABASE`, `DB_USERNAME`,
`DB_PASSWORD`, and `MYSQL_ATTR_SSL_CA=/app/ca.pem`.
