<?php
session_start();
require_once('backend/config/db.php');

$db = Database::connect();

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

if (!isset($_SESSION['user_id'])) {
    setFlash("Veuillez vous connecter");
    header('Location: auth.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$games = $db->prepare("
    SELECT g.id, g.name, g.image 
    FROM games g
    JOIN user_games ug ON ug.game_id = g.id
    WHERE ug.user_id = :id
");
$games->execute(['id' => $user_id]);
$userGames = $games->fetchAll();

$achievements = $db->prepare("
    SELECT a.name, a.image 
    FROM achievements a
    JOIN user_achievements ua ON ua.achievement_id = a.id
    WHERE ua.user_id = :id
");
$achievements->execute(['id' => $user_id]);
$userAchievements = $achievements->fetchAll();

$gameCount = count($userGames);
$successCount = count($userAchievements);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <header>
        <h2>Aetheria</h2>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <?php showFlash(); ?>

    <section>

        <h1>Mon profil</h1>

        <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>

        <hr>

        <h2>Statistiques</h2>

        <p><strong>Nombre de jeux :</strong> <?= $gameCount ?></p>
        <p><strong>Nombre de succès :</strong> <?= $successCount ?></p>

    </section>

    <hr>

    <section>

        <h2>Mes jeux</h2>

        <?php if (empty($userGames)): ?>
            <p>Vous n'avez encore aucun jeu.</p>
        <?php else: ?>

            <?php foreach ($userGames as $game): ?>
                <div>

                    <img src="Images/<?= htmlspecialchars($game['image']) ?>"
                        alt="<?= htmlspecialchars($game['name']) ?>"
                        width="100">

                    <p><?= htmlspecialchars($game['name']) ?></p>

                    <a href="games.php?id=<?= $game['id'] ?>">
                        Voir le jeu
                    </a>

                </div>

                <hr>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <section>

        <h2>Mes succès</h2>

        <?php if (empty($userAchievements)): ?>
            <p>Aucun succès débloqué pour le moment.</p>
        <?php else: ?>

            <?php foreach ($userAchievements as $achievement): ?>
                <div>

                    <img src="Images/<?= htmlspecialchars($achievement['image']) ?>"
                        alt="<?= htmlspecialchars($achievement['name']) ?>"
                        width="80">

                    <p><?= htmlspecialchars($achievement['name']) ?></p>

                </div>

                <hr>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <footer>
        <p>2026 - Projet Étudiants</p>
    </footer>

</body>

</html>