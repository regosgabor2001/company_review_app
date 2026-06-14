**Company Review App**

A small Symfony 7.4 application that collects company reviews, displays aggregated statistics, and generates automated AI summaries for companies using the Symfony Scheduler.

**Features & Stack**
- **Framework:** Symfony 7.4 & PHP 8.4
- **Database:** MySQL 8.0 & Doctrine ORM
- **Frontend:** Twig & Bootstrap
- **Testing:** PHPUnit (Unit & Functional tests)
- **Extra Feature:** Automated AI summary generation for companies via Symfony Scheduler.

---

**Requirements**
- **Docker & Compose:** Install Docker Desktop and ensure `docker compose` is available.

**Install PHP Dependencies (Composer):**
- Execute composer install inside the app container to download all required packages: `docker compose exec app composer install`

**Start (Docker)**
- **Build & run:** `docker compose up -d --build`

**Configure Environment Variables**
- The AI summary feature requires a Gemini API key. Create a .env.local file in the root directory (if it doesn't exist yet) and add your API key:

    # .env.local
    GEMINI_API_KEY="your_actual_gemini_api_key_here"

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

**Application URLs**
Once the containers are up and healthy, you can access the following services:

- Web Application: http://localhost:8080
- Company Statistics Page: http://localhost:8080/companies
- phpMyAdmin: http://localhost:8081 (User: app_user | Pass: app_password)

**Run Tests**
- Execute the test suite inside the app container:

  `docker compose exec app bin/phpunit`

**Work Log**

| Task | Time |
| :--- | :---: |
| Docker environment setup & Network/Port configuration | 1:15 h |
| Doctrine Database Model (Review, Company) & Migrations | 0:45 h |
| Symfony Form (ReviewType), CSRF protection & Validation rules | 0:45 h |
| Homepage review listing (Pagination, Search, Sorting) & Twig UI | 1:30 h |
| Writing Unit & Functional/Integration Tests (PHPUnit) | 1:15 h |
| **Bonus:** Symfony Scheduler + Messenger + AI service integration | 1:45 h |
| **Total Work Time:** | **7:15 h** |