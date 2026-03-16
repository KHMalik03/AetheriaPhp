# AetheriaPhp

REST API PHP pour la gestion d'une plateforme de jeux vidéo. Construite avec PHP 8.2, Apache (XAMPP) et PostgreSQL.

**Base URL** : `http://localhost/AetheriaPhp/api`

---

## Authentification

Les sessions sont gérées via des cookies PHP (durée : 6h).

### POST `/api/login`
Connecte un utilisateur.

**Body :**
```json
{
    "email": "user@example.com",
    "password": "motdepasse"
}
```

**Réponses :**
- `200` — `{ "message": "Login successful" }`
- `401` — `{ "message": "Invalid credentials" }`

---

### POST `/api/logout`
Déconnecte l'utilisateur courant.

**Réponses :**
- `200` — `{ "message": "Logged out" }`

---

## Users

### GET `/api/users`
Retourne la liste de tous les utilisateurs.

**Réponses :**
- `200` — Tableau d'objets utilisateur

---

### GET `/api/users/{id}`
Retourne un utilisateur par son ID.

**Réponses :**
- `200` — Objet utilisateur
- `404` — `{ "message": "User not found" }`

---

### POST `/api/users`
Crée un nouvel utilisateur.

**Body :**
```json
{
    "username": "john",
    "email": "john@example.com",
    "password": "motdepasse"
}
```

**Réponses :**
- `201` — `{ "message": "User created successfully" }`
- `500` — `{ "message": "Failed to create user" }`

---

### PUT `/api/users/{id}`
Met à jour les informations d'un utilisateur.

**Body :**
```json
{
    "username": "john_updated",
    "email": "john@example.com",
    "description": "Ma description"
}
```

**Réponses :**
- `200` — `{ "message": "User updated successfully" }`
- `404` — `{ "message": "User not found" }`

---

### DELETE `/api/users/{id}`
Supprime un utilisateur.

**Réponses :**
- `200` — `{ "message": "User deleted successfully" }`
- `404` — `{ "message": "User not found" }`

---

### PATCH `/api/users/{id}/role`
Change le rôle d'un utilisateur. Réservé aux admins.

**Body :**
```json
{
    "role": "admin"
}
```

**Réponses :**
- `200` — `{ "message": "Role updated successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`

---

## Games

### GET `/api/games`
Retourne la liste de tous les jeux.

**Réponses :**
- `200` — Tableau d'objets jeu

---

### GET `/api/games/{id}`
Retourne un jeu par son ID.

**Réponses :**
- `200` — Objet jeu
- `404` — `{ "message": "Game not found" }`

---

### POST `/api/games`
Crée un nouveau jeu. Réservé aux admins.

**Body :**
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

**Réponses :**
- `201` — `{ "message": "Game created successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`

---

### PUT `/api/games/{id}`
Met à jour un jeu. Réservé aux admins.

**Réponses :**
- `200` — `{ "message": "Game updated successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`
- `404` — `{ "message": "Game not found" }`

---

### DELETE `/api/games/{id}`
Supprime un jeu. Réservé aux admins.

**Réponses :**
- `200` — `{ "message": "Game deleted successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`
- `404` — `{ "message": "Game not found" }`

---

## Achievements

### GET `/api/achievements/game/{game_id}`
Retourne tous les trophées d'un jeu.

**Réponses :**
- `200` — Tableau d'objets achievement

---

### POST `/api/achievements`
Crée un trophée. Réservé aux admins.

**Body :**
```json
{
    "game_id": 1,
    "title": "Premier sang",
    "description": "Vaincre un ennemi pour la première fois"
}
```

**Réponses :**
- `201` — `{ "message": "Achievement created successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`

---

### PUT `/api/achievements/{id}`
Met à jour un trophée.

**Body :**
```json
{
    "title": "Nouveau titre",
    "description": "Nouvelle description"
}
```

**Réponses :**
- `200` — `{ "message": "Achievement updated successfully" }`
- `404` — `{ "message": "Achievement not found" }`

---

### DELETE `/api/achievements/{id}`
Supprime un trophée.

**Réponses :**
- `200` — `{ "message": "Achievement deleted successfully" }`
- `404` — `{ "message": "Achievement not found" }`

---

## Levels

Les levels représentent la difficulté associée à un jeu.

### GET `/api/levels/{difficulty}/games`
Retourne tous les jeux d'une difficulté donnée (`easy`, `medium`, `hard`).

**Réponses :**
- `200` — Tableau d'objets jeu

---

### POST `/api/levels`
Associe un jeu à une difficulté. Réservé aux admins.

**Body :**
```json
{
    "game_id": 1,
    "difficulty": "easy",
    "description": "Accessible aux débutants"
}
```

**Réponses :**
- `201` — `{ "message": "Game added to level successfully" }`
- `403` — `{ "message": "Forbidden: admins only" }`

---

## User Games

Gestion de la bibliothèque de jeux d'un utilisateur.

### GET `/api/users/{id}/games`
Retourne tous les jeux d'un utilisateur avec la date d'ajout et le temps de jeu.

**Réponses :**
- `200` — Tableau d'objets jeu enrichis (`date_added`, `play_time`)

---

### POST `/api/users/{id}/games`
Ajoute un jeu à la bibliothèque d'un utilisateur. Nécessite d'être connecté.

**Body :**
```json
{
    "game_id": 1
}
```

**Réponses :**
- `201` — `{ "message": "Game added to user successfully" }`
- `401` — `{ "message": "Unauthorized" }`
- `400` — `{ "message": "Invalid JSON body" }`

---

### DELETE `/api/users/{id}/games/{game_id}`
Retire un jeu de la bibliothèque d'un utilisateur. Nécessite d'être connecté.

**Réponses :**
- `200` — `{ "message": "Game removed from user successfully" }`
- `401` — `{ "message": "Unauthorized" }`

---

## User Achievements

Gestion des trophées débloqués par un utilisateur.

### GET `/api/users/{id}/achievements`
Retourne tous les trophées débloqués par un utilisateur avec la date de débloquage.

**Réponses :**
- `200` — Tableau d'objets achievement enrichis (`unlocked_at`)

---

### POST `/api/users/{id}/achievements`
Débloque un trophée pour un utilisateur. Nécessite d'être connecté.

**Body :**
```json
{
    "achievement_id": 1
}
```

**Réponses :**
- `201` — `{ "message": "Achievement unlocked successfully" }`
- `400` — `{ "message": "achievement_id is required" }`
- `401` — `{ "message": "Unauthorized" }`
