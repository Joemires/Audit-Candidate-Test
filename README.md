
## Objectives
To develop an API-based Auditor Management System using PHP (Symfony) that enables auditors to manage their schedules, assign themselves to jobs, record job completion, and provide an assessment of their work. This system will also consider different time zones (Madrid, Mexico City, and the United Kingdom) to ensure accurate scheduling and reporting.

## Installation
Clone the project locally and navigate to the root of the project

```bash
git clone https://github.com/Joemires/Audit-Candidate-Test.git && cd Audit-Candidate-Test
```

Install composer dependencies
```bash 
composer update
```

Setup environment variables
```bash 
cp .env .env.local
```

Generate application JWT SSL keys
```bash
php bin/console lexik:jwt:generate-keypair
```

Configure your database type in the .env.local and start your docker container if database is using a docker image
Start docker container if need be and create database
```bash
docker compose up -d
php bin/console doctrine:database:create
```

Setup database migration
```bash
php bin/console doctrine:migrations:migrate
```

Once the app has been configured, you can now serve your application and explore
```bash
symfony server:start --no-tls
```

Application should run with no issues, open ``http://127.0.0.1:8000`` to be sure application is running smoothly

Now open ``https://documenter.getpostman.com/view/7800956/2s9YJXZ4xK`` to follow up with the API documentation on PostMan