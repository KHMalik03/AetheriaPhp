<?php
session_start();
require_once 'backend/config/db.php';

$pdo = Database::connect();

function setFlash($msg)
{
    $_SESSION['flash'] = $msg;
}

function showFlash()
{
    if (!empty($_SESSION['flash'])) {
        echo "<div>" . htmlspecialchars($_SESSION['flash']) . "</div>";
        unset($_SESSION['flash']);
    }
}

$isLogged = isset($_SESSION['user_id']);

$stmt = $pdo->query("SELECT * FROM games");
$games = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <header>
        <div>
            <strong>Aetheria</strong>
        </div>

        <nav>
            <a href="index.php">Accueil</a>

            <?php if ($isLogged): ?>
                <a href="user.php">Profil</a>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="auth.php">Connexion / Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php showFlash(); ?>

    <h1>Jeux de la Saga</h1>

    <?php if (empty($games)): ?>
        <p>Aucun jeu disponible pour le moment.</p>
    <?php else: ?>

        <section>

            <?php foreach ($games as $game): ?>
                <div>

                    <div>
                        <img src="/frontend/Images/<?= htmlspecialchars($game['image']) ?>"
                            alt="<?= htmlspecialchars($game['name']) ?>"
                            width="200">
                    </div>

                    <div>
                        <h3><?= htmlspecialchars($game['name']) ?></h3>

                        <p><?= htmlspecialchars($game['description']) ?></p>

                        <p><strong>Prix : <?= htmlspecialchars($game['price']) ?> €</strong></p>
                    </div>

                    <div>
                        <a href="games.php?id=<?= $game['id'] ?>">
                            <button>Voir le jeu</button>
                        </a>
                    </div>

                </div>

                <hr>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

    <footer>
        <p>2026 - Projet Étudiants</p>
    </footer>

</body>

</html>