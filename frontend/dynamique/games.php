<?php
function apiCall(string $method, string $endpoint, array $data = []): array {
    $ch = curl_init('http://localhost/AetheriaPhp/api' . $endpoint);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIE         => 'PHPSESSID=' . ($_COOKIE['PHPSESSID'] ?? ''),
        CURLOPT_CUSTOMREQUEST  => $method,
    ];
    if (!empty($data)) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($data);
        $opts[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'body' => json_decode($response, true) ?? []];
}

function setFlash(string $msg): void { $_SESSION['flash'] = $msg; }
function showFlash(): void {
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

$game_id = (int)$_GET['id'];

$gameRes = apiCall('GET', '/games/' . $game_id);
if ($gameRes['code'] === 404 || empty($gameRes['body'])) {
    setFlash("Jeu introuvable");
    header('Location: ../../index.php');
    exit();
}
$game = $gameRes['body'];

$achievementsRes  = apiCall('GET', '/achievements/game/' . $game_id);
$gameAchievements = $achievementsRes['body'] ?? [];

$owned  = false;
$meRes  = apiCall('GET', '/me');
$meUser = $meRes['code'] === 200 ? $meRes['body'] : null;

if ($meUser) {
    $userGamesRes = apiCall('GET', '/users/' . $meUser['id'] . '/games');
    $userGames    = $userGamesRes['body'] ?? [];
    foreach ($userGames as $ug) {
        if ((int)$ug['id'] === $game_id) {
            $owned = true;
            break;
        }
    }
}

if (isset($_POST['buy'])) {
    if (!$meUser) {
        header("Location: /AetheriaPhp/frontend/dynamique/auth.php");
        exit();
    }

    if (!$owned) {
        $result = apiCall('POST', '/users/' . $meUser['id'] . '/games', ['game_id' => $game_id]);
        setFlash($result['code'] === 201 ? "Jeu ajouté à votre bibliothèque !" : "Erreur lors de l'ajout");
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
    <link rel="stylesheet" href="/AetheriaPhp/frontend/statics/games.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="../../Images/logo.png" alt="Logo">
        <strong>Aetheria</strong>
    </div>

    <nav>
        <a href="../../index.php">Accueil</a>

        <?php if ($meUser): ?>
            <a href="user.php">Profil</a>
            <a href="../../backend/auth/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="auth.php">Connexion</a>
        <?php endif; ?>
    </nav>
</header>

<?php showFlash(); ?>

<div class="container">

    <div class="game-card">

        <div class="game-image">
            <img src="<?= htmlspecialchars($game['image_url'] ?? '') ?>"
                 alt="<?= htmlspecialchars($game['name']) ?>">
        </div>

        <div class="game-info">
            <h2><?= htmlspecialchars($game['name']) ?></h2>

            <p><strong>Type :</strong> <?= htmlspecialchars($game['type'] ?? '-') ?></p>
            <p><strong>Studio :</strong> <?= htmlspecialchars($game['studio'] ?? '-') ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($game['release_date'] ?? '-') ?></p>

            <p><?= htmlspecialchars($game['description'] ?? '') ?></p>

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
                    <button name="buy">Ajouter à ma bibliothèque</button>
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
