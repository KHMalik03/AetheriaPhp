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

if (!isset($_SESSION['user_id'])) {
    setFlash("Veuillez vous connecter");
    header('Location: auth.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT username, email, role FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: auth.php');
    exit();
}

$games = $db->prepare("
    SELECT g.id, g.name, g.image_url 
    FROM games g
    JOIN user_games ug ON ug.game_id = g.id
    WHERE ug.user_id = :id
");
$games->execute(['id' => $user_id]);
$userGames = $games->fetchAll();

$achievements = $db->prepare("
    SELECT a.title, a.description 
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
    <link rel="stylesheet" href="../../frontend/statics/user.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="../../Images/logo.png" alt="Logo">
        <strong>Aetheria</strong>
    </div>

    <nav>
        <a href="../../index.php">Accueil</a>

        <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
            <a href="../../frontend/dynamique/admin.php">Admin</a>
        <?php endif; ?>

        <a href="../../backend/auth/logout.php">Déconnexion</a>
    </nav>
</header>

<?php showFlash(); ?>

<div class="profile-container">

    <div class="profile-card">

        <img src="../../Images/avatar.jpg" class="avatar" alt="Avatar">

        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username']) ?></h2>

            <div class="info-grid">
                <div>
                    <p><strong>Email :</strong></p>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <div>
                    <p><strong>Jeux :</strong></p>
                    <p><?= $gameCount ?></p>
                </div>

                <div>
                    <p><strong>Succès :</strong></p>
                    <p><?= $successCount ?></p>
                </div>
            </div>
        </div>

    </div>

    <div class="bottom-section">

        <div class="games-box">

            <?php if (!empty($userGames)): ?>
                <img src="../../Images/<?= htmlspecialchars($userGames[0]['image_url']) ?>" alt="">
            <?php endif; ?>

            <a class="link" href="../../index.php">Voir mes jeux</a>

        </div>

        <div class="achievements-box">

            <img src="../../Images/trophy.png" alt="Succès">

            <span class="link"><?= $successCount ?> succès</span>

        </div>

    </div>

</div>

<footer>
    <p>2026 - Projet Étudiants</p>
</footer>

</body>
</html>