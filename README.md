# AetheriaPhp — Backend API

API REST pour la plateforme de jeux vidéo Aetheria. Elle gère les utilisateurs, les jeux, les trophées, les niveaux de difficulté et les bibliothèques personnelles des joueurs.

---

## Stack technique

- **PHP 8.2** — logique métier et routing
- **Apache** via XAMPP — serveur web local / Docker — serveur web conteneurisé
- **PostgreSQL** — base de données
- **Sessions PHP** — authentification (cookies, durée 6h)
- **Docker** — conteneurisation avec séparation frontend / backend

---

## Docker

### Architecture

```
Navigateur
    ↓ port 80
 Container frontend  (PHP 8.2 + Apache) — pages web
    ↓ http://backend/api  (réseau Docker interne)
 Container backend   (PHP 8.2 + Apache) — API REST
    ↓
 Container db        (PostgreSQL latest) — base de données
```

### Fichiers Docker

| Fichier | Rôle |
|---------|------|
| `Dockerfile` | Image PHP 8.2 + Apache + extensions PDO PostgreSQL |
| `Dockerfile.db` | Image PostgreSQL avec schéma SQL intégré |
| `docker-compose.yml` | Orchestration des 3 containers |
| `.env` | Variables d'environnement (credentials DB, URL API) |
| `.htaccess` | Réécriture des routes `/api/*` vers `backend/api.php` |
| `apache.conf` | Active `AllowOverride All` pour Apache |

### Lancer le projet

```bash
# Premier lancement
docker compose up --build

# Relancer sans rebuild
docker compose up

# Arrêter (données conservées)
docker compose down

# Arrêter et supprimer les données
docker compose down -v
```

### Variables d'environnement (`.env`)

```env
DB_HOST=**
DB_PORT=****
DB_NAME=******
DB_USER=*****
DB_PASS=*******
```

### Accès

| Service | URL |
|---------|-----|
| Frontend | http://localhost |
| Backend API | http://localhost:8080 |

---

## Frontend

### Pages

| Fichier | URL | Description |
|---------|-----|-------------|
| `index.php` | `/` | Accueil — liste des jeux |
| `frontend/dynamique/auth.php` | `/frontend/dynamique/auth.php` | Connexion / Inscription |
| `frontend/dynamique/games.php` | `/frontend/dynamique/games.php?id={id}` | Détail d'un jeu |
| `frontend/dynamique/user.php` | `/frontend/dynamique/user.php` | Profil utilisateur |
| `frontend/dynamique/admin.php` | `/frontend/dynamique/admin.php` | Panel admin |

### Configuration API (`frontend/dynamique/config.php`)

L'URL de l'API est centralisée dans ce fichier et peut être surchargée via la variable d'environnement `API_URL` :

```php
define('API_URL', getenv('API_URL') ?: 'http://localhost/api');
```

En local (XAMPP) : `http://localhost/AetheriaPhp/api`
En Docker : `http://backend/api` (défini automatiquement via `docker-compose.yml`)

### Modifications apportées pour Docker

- Suppression du préfixe `/AetheriaPhp/` dans tous les chemins CSS et redirections
- Centralisation de l'URL de l'API dans `frontend/dynamique/config.php`
- `index.php` utilise l'API `/me` pour vérifier l'authentification (au lieu de `$_SESSION`)
- `backend/auth/logout.php` appelle l'API `/api/logout` pour détruire la session côté backend

---
## Authentification

L'API utilise des **sessions PHP via cookies**. Pour accéder aux endpoints protégés, il faut d'abord se connecter via `/api/login`. Le cookie de session est automatiquement envoyé et doit être inclus dans toutes les requêtes suivantes.

Deux niveaux d'accès :
- **Connecté** (`isLoggedIn`) — accès aux actions personnelles (bibliothèque, trophées)
- **Admin** (`isAdmin`) — accès à la gestion du contenu (jeux, trophées, niveaux)

---

## Endpoints

### Authentification

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| POST | `/api/login` | — | Connexion |
| POST | `/api/logout` | — | Déconnexion |

#### POST `/api/login`
```json
{
    "email": "user@example.com",
    "password": "motdepasse"
}
```
Retourne `200` en cas de succès, `401` si les identifiants sont incorrects.

---

### Users

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/users` | — | Liste tous les utilisateurs |
| GET | `/api/users/{id}` | — | Détail d'un utilisateur |
| POST | `/api/users` | — | Créer un compte |
| PUT | `/api/users/{id}` | — | Modifier un utilisateur |
| DELETE | `/api/users/{id}` | — | Supprimer un utilisateur |
| PATCH | `/api/users/{id}/role` | Admin | Changer le rôle |

#### GET `/api/users` — exemple de réponse
```json
[
    {
        "id": 1,
        "username": "john",
        "email": "john@example.com",
        "role": "user",
        "created_at": "2024-01-01 10:00:00",
        "description": "Joueur passionné"
    }
]
```

#### POST `/api/users`
```json
{
    "username": "john",
    "email": "john@example.com",
    "password": "motdepasse"
}
```

#### PUT `/api/users/{id}`
```json
{
    "username": "john_updated",
    "email": "john@example.com",
    "description": "Ma nouvelle description"
}
```

#### PATCH `/api/users/{id}/role`
```json
{
    "role": "admin"
}
```

---

### Games

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/games` | — | Liste tous les jeux |
| GET | `/api/games/{id}` | — | Détail d'un jeu |
| POST | `/api/games` | Admin | Créer un jeu |
| PUT | `/api/games/{id}` | Admin | Modifier un jeu |
| DELETE | `/api/games/{id}` | Admin | Supprimer un jeu |

#### GET `/api/games` — exemple de réponse
```json
[
    {
        "id": 1,
        "name": "The Witcher 3",
        "type": "RPG",
        "description": "Un RPG open world",
        "image_url": "https://...",
        "release_date": "2015-05-19",
        "studio": "CD Projekt Red",
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00"
    }
]
```

#### POST `/api/games`
```json
{
    "name": "The Witcher 3",
    "type": "RPG",
    "description": "Un RPG open world",
    "image_url": "https://...",
    "release_date": "2015-05-19",
    "studio": "CD Projekt Red"
}
```

---

### Achievements (Trophées)

Les trophées sont liés à un jeu et peuvent être débloqués par les utilisateurs.

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/achievements/game/{game_id}` | — | Trophées d'un jeu |
| POST | `/api/achievements` | Admin | Créer un trophée |
| PUT | `/api/achievements/{id}` | — | Modifier un trophée |
| DELETE | `/api/achievements/{id}` | — | Supprimer un trophée |

#### GET `/api/achievements/game/{game_id}` — exemple de réponse
```json
[
    {
        "id": 1,
        "game_id": 1,
        "title": "Premier sang",
        "description": "Vaincre un ennemi pour la première fois"
    }
]
```

#### POST `/api/achievements`
```json
{
    "game_id": 1,
    "title": "Premier sang",
    "description": "Vaincre un ennemi pour la première fois"
}
```

#### PUT `/api/achievements/{id}`
```json
{
    "title": "Nouveau titre",
    "description": "Nouvelle description"
}
```

---

### Levels (Difficulté des jeux)

Un level associe un jeu à un niveau de difficulté (`easy`, `medium`, `hard`).

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/levels/{difficulty}/games` | — | Jeux d'une difficulté |
| POST | `/api/levels` | Admin | Associer un jeu à une difficulté |

#### GET `/api/levels/easy/games` — exemple de réponse
```json
[
    {
        "id": 1,
        "name": "The Witcher 3",
        "type": "RPG",
        "studio": "CD Projekt Red"
    }
]
```

#### POST `/api/levels`
```json
{
    "game_id": 1,
    "difficulty": "easy",
    "description": "Accessible aux débutants"
}
```

---

### Bibliothèque de jeux d'un utilisateur

Jeux ajoutés par un utilisateur à sa bibliothèque personnelle.

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/users/{id}/games` | — | Bibliothèque d'un utilisateur |
| POST | `/api/users/{id}/games` | Connecté | Ajouter un jeu |
| DELETE | `/api/users/{id}/games/{game_id}` | Connecté | Retirer un jeu |

#### GET `/api/users/{id}/games` — exemple de réponse
```json
[
    {
        "id": 1,
        "name": "The Witcher 3",
        "type": "RPG",
        "date_added": "2024-01-15 14:30:00",
        "play_time": 120
    }
]
```

#### POST `/api/users/{id}/games`
```json
{
    "game_id": 1
}
```

---

### Trophées débloqués par un utilisateur

Un trophée ne peut être qu'ajouté, jamais supprimé.

| Méthode | URL | Auth | Description |
|---------|-----|------|-------------|
| GET | `/api/users/{id}/achievements` | — | Trophées débloqués |
| POST | `/api/users/{id}/achievements` | Connecté | Débloquer un trophée |

#### GET `/api/users/{id}/achievements` — exemple de réponse
```json
[
    {
        "id": 1,
        "game_id": 1,
        "title": "Premier sang",
        "description": "Vaincre un ennemi pour la première fois",
        "unlocked_at": "2024-02-10 18:45:00"
    }
]
```

#### POST `/api/users/{id}/achievements`
```json
{
    "achievement_id": 1
}
```

---

## Codes de réponse

| Code | Signification |
|------|---------------|
| `200` | Succès |
| `201` | Ressource créée |
| `400` | Body invalide ou champ manquant |
| `401` | Non connecté |
| `403` | Accès refusé (admin requis) |
| `404` | Ressource introuvable |
| `500` | Erreur serveur |
