### Documentation du projet

## Stack
- Symfony 7.3.7
- MySQL 8 (via Docker)
- Doctrine ORM
- Twig

## Pré-requis
- PHP 8.2 minimum 
- Docker
- Node.js

## Installation locale

### 1. Cloner le projet
```bash
git clone https://github.com/lazarefortune/adopte-challenge.git
cd adopte-challenge
```

### 2. Lancer MySQL via Docker
```bash
docker compose up -d
```

### 3. Copier la config et installer les dépendances
```bash
cp .env .env.local
```

```bash
composer install
```

#### Installer les dépendances front
Avec PNPM :
```bash
pnpm install 
``` 
ou avc NPM
```bash
npm install 
``` 

### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les fixtures
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### 6. Comptes de test
| Email                                       | Mot de passe | Rôles       |
| ------------------------------------------- | ------------ | ----------- |
| admin@gmail.com | Password123!     | ROLE\_ADMIN |
| jean@gmail.com  | Password123!      | ROLE\_USER  |


### Renouvellement automatique des abonnements

Une commande gère le renouvellement :
```bash
php bin/console app:subscriptions:renew
```

À programmer via un cron Linux :
```bash
* * * * * php /path/to/project/bin/console app:subscriptions:renew >> var/log/cron.log 2>&1
```


### Tests manuels à effectuer

Souscription à un abonnement
Mise à jour de carte bancaire
Renouvellement automatique avec la commande
Back-office : listing users, achats, détails utilisateur

