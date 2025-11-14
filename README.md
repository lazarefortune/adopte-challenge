### Documentation du projet


## Stack

- Symfony 7.3.7
- MySQL 8 (via Docker)
- Doctrine ORM
- Twig

## Pré-requis

- PHP 8.2 minimum 
- Docker

## Installation locale

### 1. Cloner le projet

```bash
git clone ...
cd ...
```

### 2. Lancer MySQL via Docker

```bash
docker compose up -d
```

### 3. Copier la config et installer les dépendances

```bash
cp .env .env.local

composer install
``` 

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les fixtures
```bash
php bin/console hautelook:fixtures:load --no-interaction
```

### 6. Comptes de test
| Email                                       | Mot de passe | Rôles       |
| ------------------------------------------- | ------------ | ----------- |
| admin@gmail.com | Password123!     | ROLE\_ADMIN |
| jean@gmail.com  | Password123!      | ROLE\_USER  |
