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

$apiUrl = getenv('API_URL') ?: 'http://localhost/api';
$ch = curl_init($apiUrl . '/me');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIE         => 'PHPSESSID=' . ($_COOKIE['PHPSESSID'] ?? ''),
]);
$meResponse = curl_exec($ch);
$meCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$isLogged = $meCode === 200;

$stmt = $pdo->query("
    SELECT id, name, type, description, image_url, release_date, studio
    FROM games
    ORDER BY created_at DESC
");

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontend/statics/index.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="Images/logo.png" alt="Logo">
        <strong>Aetheria</strong>
    </div>

    <nav>
        <a href="index.php">Accueil</a>

        <?php if ($isLogged): ?>
            <a href="frontend/dynamique/user.php">Profil</a>
            <a href="backend/auth/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="frontend/dynamique/auth.php">Connexion / Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<?php showFlash(); ?>

<h1 class="main-title">Jeux de la Saga</h1>

<?php if (empty($games)): ?>
    <p style="text-align:center;">Aucun jeu disponible pour le moment.</p>
<?php else: ?>

<section class="grid">

    <?php foreach ($games as $game): ?>
        
        <div class="card">

            <img src="Images/<?= htmlspecialchars($game['image_url']) ?>"
                 alt="<?= htmlspecialchars($game['name']) ?>">

            <div class="card-content">
                <h3><?= htmlspecialchars($game['name']) ?></h3>

                <p><?= htmlspecialchars($game['description']) ?></p>

                <a href="frontend/dynamique/games.php?id=<?= $game['id'] ?>">
                    <button>Voir le jeu</button>
                </a>
            </div>

        </div>

    <?php endforeach; ?>

</section>

<?php endif; ?>

<footer>
    <p>2026 - Projet Étudiants</p>
</footer>

</body>
</html>