<?php
session_start();
require_once('../../backend/config/db.php');

$db = Database::connect();

function setFlash($msg)
{
    $_SESSION['flash'] = $msg;
}

function showFlash()
{
    if (!empty($_SESSION['flash'])) {
        echo "<div style='text-align:center; padding:10px;'>" . htmlspecialchars($_SESSION['flash']) . "</div>";
        unset($_SESSION['flash']);
    }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlash("Jeu introuvable");
    header('Location: ../../index.php');
    exit();
}

$game_id = (int) $_GET['id'];

$stmt = $db->prepare("
    SELECT id, name, description, image_url, type, release_date, studio
    FROM games 
    WHERE id = :id
");
$stmt->execute(['id' => $game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    setFlash("Jeu introuvable");
    header('Location: ../../index.php');
    exit();
}

$achievements = $db->prepare("
    SELECT id, title, description 
    FROM achievements 
    WHERE game_id = :id
");
$achievements->execute(['id' => $game_id]);
$gameAchievements = $achievements->fetchAll(PDO::FETCH_ASSOC);

$owned = false;

if (isset($_SESSION['user_id'])) {
    $check = $db->prepare("
        SELECT 1 FROM user_games 
        WHERE user_id = :user_id AND game_id = :game_id
    ");
    $check->execute([
        'user_id' => $_SESSION['user_id'],
        'game_id' => $game_id
    ]);

    $owned = (bool) $check->fetch();
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
    <link rel="stylesheet" href="../../frontend/statics/games.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="../../Images/logo.png" alt="Logo">
        <strong>Aetheria</strong>
    </div>

    <nav>
        <a href="../../index.php">Accueil</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../../frontend/dynamique/user.php">Profil</a>
            <a href="../../backend/auth/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="../../frontend/dynamique/auth.php">Connexion</a>
        <?php endif; ?>
    </nav>
</header>

<?php showFlash(); ?>

<div class="container">

    <div class="game-card">

        <div class="game-image">
            <img src="../../Images/<?= htmlspecialchars($game['image_url']) ?>"
                 alt="<?= htmlspecialchars($game['name']) ?>">
        </div>

        <div class="game-info">
            <h2><?= htmlspecialchars($game['name']) ?></h2>

            <p><strong>Type :</strong> <?= htmlspecialchars($game['type']) ?></p>
            <p><strong>Studio :</strong> <?= htmlspecialchars($game['studio']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($game['release_date']) ?></p>

            <p><?= htmlspecialchars($game['description']) ?></p>

            <h3>Succès</h3>

            <div class="achievements">

                <?php if (empty($gameAchievements)): ?>
                    <p>Aucun succès</p>
                <?php else: ?>

                    <?php foreach ($gameAchievements as $achievement): ?>
                        <div class="achievement">

                            <img src="../../Images/trophy.png" alt="Succès">

                            <p>
                                <?php if ($owned): ?>
                                    <?= htmlspecialchars($achievement['title']) ?>
                                <?php else: ?>
                                    🔒 Verrouillé
                                <?php endif; ?>
                            </p>

                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>

            </div>
        </div>

        <div class="price-box">

            <?php if ($owned): ?>
                <button disabled>Déjà possédé</button>
            <?php else: ?>
                <form method="POST">
                    <button name="buy">Acheter</button>
                </form>
            <?php endif; ?>

        </div>

    </div>

</div>

<footer>
    <p>2026 - Projet Étudiants</p>
</footer>

</body>
</html>