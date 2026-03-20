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

if (!isset($_GET['id'])) {
    setFlash("Jeu introuvable");
    header('Location: index.php');
    exit();
}

$game_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM games WHERE id = :id");
$stmt->execute(['id' => $game_id]);
$game = $stmt->fetch();

if (!$game) {
    setFlash("Jeu introuvable");
    header('Location: index.php');
    exit();
}

$achievements = $db->prepare("
    SELECT * FROM achievements WHERE game_id = :id
");
$achievements->execute(['id' => $game_id]);
$gameAchievements = $achievements->fetchAll();

$owned = false;

if (isset($_SESSION['user_id'])) {
    $check = $db->prepare("
        SELECT * FROM user_games 
        WHERE user_id = :user_id AND game_id = :game_id
    ");
    $check->execute([
        'user_id' => $_SESSION['user_id'],
        'game_id' => $game_id
    ]);

    $owned = $check->fetch() ? true : false;
}

if (isset($_POST['buy'])) {

    if (!isset($_SESSION['user_id'])) {
        setFlash("Vous devez être connecté pour acheter");
        header("Location: auth.php");
        exit();
    }

    if (!$owned) {
        $stmt = $db->prepare("
            INSERT INTO user_games (user_id, game_id) 
            VALUES (:user_id, :game_id)
        ");

        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'game_id' => $game_id
        ]);

        setFlash("Jeu acheté avec succès !");
    } else {
        setFlash("Vous possédez déjà ce jeu");
    }

    header("Location: games.php?id=" . $game_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($game['name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <header>
        <div>
            <strong>Aetheria</strong>
        </div>

        <nav>
            <a href="index.php">Accueil</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user.php">Profil</a>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="auth.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php showFlash(); ?>

    <section>

        <h1><?= htmlspecialchars($game['name']) ?></h1>

        <div>
            <img src="Images/<?= htmlspecialchars($game['image']) ?>"
                alt="<?= htmlspecialchars($game['name']) ?>"
                width="300">
        </div>

        <p><strong>Date :</strong> <?= htmlspecialchars($game['release_date']) ?></p>
        <p><strong>Editeur :</strong> <?= htmlspecialchars($game['editor']) ?></p>

        <p>
            <strong>Description :</strong><br>
            <?= htmlspecialchars($game['description']) ?>
        </p>

    </section>

    <section>

        <h2>Succès</h2>

        <?php if (empty($gameAchievements)): ?>
            <p>Aucun succès pour ce jeu</p>
        <?php else: ?>

            <?php foreach ($gameAchievements as $achievement): ?>
                <div>

                    <img src="Images/<?= htmlspecialchars($achievement['image']) ?>"
                        alt="<?= htmlspecialchars($achievement['name']) ?>"
                        width="80">

                    <p>
                        <?php if ($owned): ?>
                            <?= htmlspecialchars($achievement['name']) ?>
                        <?php else: ?>
                            🔒 Succès verrouillé (achetez le jeu)
                        <?php endif; ?>
                    </p>

                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <section>

        <h2>Achat</h2>

        <p><strong>Prix : <?= htmlspecialchars($game['price']) ?> €</strong></p>

        <?php if ($owned): ?>
            <button disabled>Déjà possédé</button>

        <?php else: ?>

            <form method="POST">
                <button name="buy">Acheter</button>
            </form>

        <?php endif; ?>

    </section>

    <footer>
        <p>2026 - Projet Étudiants</p>
    </footer>

</body>

</html>