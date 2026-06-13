**Company Review App**

A small Symfony app that collects company reviews and generates AI summaries.

**Features & Stack**
- **Framework:** Symfony 7.4 & PHP 8.2+
- **Database:** MySQL 8.0 & Doctrine ORM
- **Frontend:** Twig & Bootstrap
- **Testing:** PHPUnit (Unit & Functional tests)
- **Extra Feature:** Automated AI summary generation for companies via Symfony Scheduler.

---

**Requirements**
- **Docker & Compose:** Install Docker Desktop and ensure `docker compose` is available.

**Start (Docker)**
- **Build & run:** `docker compose up -d --build`

**Start Scheduler**
- This project uses the Symfony Scheduler. The schedule provider for company AI reviews is named `ai_company_summary`.
- Run the scheduler consumer (open a separate terminal so it runs continuously):

  `docker compose exec app bin/console messenger:consume scheduler_ai_company_summary`

**Create Database(s) & Load Fixtures**

- Create and migrate the main (development) database and load fixtures:

  `docker compose exec app bin/console doctrine:database:create`

  `docker compose exec app bin/console doctrine:migrations:migrate`

  `docker compose exec app bin/console doctrine:fixtures:load`

- Create and migrate the test database and load fixtures (useful for CI/local test runs):

  `docker compose exec app bin/console doctrine:database:create --env=test --no-interaction`

  `docker compose exec app bin/console doctrine:migrations:migrate --env=test --no-interaction`

**Run Tests**
- Execute the test suite inside the app container:

  `docker compose exec app bin/phpunit`
