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

$meRes = apiCall('GET','/me');
if ($meRes['code'] !== 200) {
    header('Location: /AetheriaPhp/frontend/dynamique/auth.php');
    exit();
}

$user_id = (int)$meRes['body']['id'];

$userRes  = apiCall('GET','/users/' . $user_id);
$user     = $userRes['body'] ?? [];

$gamesRes        = apiCall('GET','/users/' . $user_id . '/games');
$userGames       = $gamesRes['body'] ?? [];

$achievementsRes  = apiCall('GET','/users/' . $user_id . '/achievements');
$userAchievements = $achievementsRes['body'] ?? [];

$gameCount    = count($userGames);
$successCount = count($userAchievements);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/AetheriaPhp/frontend/statics/user.css">
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
            <a href="admin.php">Admin</a>
        <?php endif; ?>

        <a href="../../backend/auth/logout.php">Déconnexion</a>
    </nav>
</header>

<?php showFlash(); ?>

<div class="profile-container">

    <div class="profile-card">

        <img src="../../Images/avatar.jpg" class="avatar" alt="Avatar">

        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username'] ?? '') ?></h2>

            <div class="info-grid">
                <div>
                    <p><strong>Email :</strong></p>
                    <p><?= htmlspecialchars($user['email'] ?? '') ?></p>
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
                <img src="<?= htmlspecialchars($userGames[0]['image_url'] ?? '') ?>" alt="">
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
