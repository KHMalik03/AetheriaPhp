<?php

require_once __DIR__ . '/config.php';

$message = "";

function apiPost(string $endpoint, array $data): array {
    $ch = curl_init(API_URL . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_HEADER         => true,
    ]);
    $response   = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headers    = substr($response, 0, $headerSize);
    $body       = substr($response, $headerSize);
    curl_close($ch);

    $sessId = null;
    foreach (explode("\r\n", $headers) as $header) {
        if (stripos($header, 'Set-Cookie: PHPSESSID=') === 0) {
            preg_match('/PHPSESSID=([^;]+)/', $header, $matches);
            $sessId = $matches[1] ?? null;
        }
    }

    return ['code' => $httpCode, 'body' => json_decode($body, true), 'sessId' => $sessId];
}

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = apiPost('/login', [
        'email'    => $_POST['email'],
        'password' => $_POST['password'],
    ]);

    if ($result['code'] === 200) {
        if (!empty($result['sessId'])) {
            setcookie('PHPSESSID', $result['sessId'], ['path' => '/', 'httponly' => true]);
        }
        header('Location: /index.php');
        exit();
    } else {
        $message = $result['body']['message'] ?? "Email ou mot de passe incorrect";
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $result = apiPost('/users', [
        'username' => $_POST['username'],
        'email'    => $_POST['email'],
        'password' => $_POST['password'],
    ]);

    if ($result['code'] === 201) {
        $message = "Compte créé avec succès, vous pouvez vous connecter";
    } else {
        $message = $result['body']['message'] ?? "Erreur lors de la création du compte";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/statics/auth.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="../../Images/logo.png" alt="Logo">
        <strong>Aetheria</strong>
    </div>
    <nav>
        <a href="../../index.php">Accueil</a>
    </nav>
</header>

<?php if (!empty($message)): ?>
    <div class="error"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="container">

    <div class="form-box">

        <input type="radio" name="tab" id="login" checked>
        <input type="radio" name="tab" id="register">

        <div class="tabs">
            <label for="login" class="tab">Connexion</label>
            <label for="register" class="tab">Inscription</label>
        </div>

        <form method="POST" class="form login-form">
            <input type="hidden" name="action" value="login">

            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <button class="main-btn" type="submit">Se connecter</button>
        </form>

        <form method="POST" class="form register-form">
            <input type="hidden" name="action" value="register">

            <label>Pseudo :</label>
            <input type="text" name="username" required>

            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <button class="main-btn" type="submit">S'inscrire</button>
        </form>

    </div>

</div>

<footer>
    <p>2026 - Projet Étudiants</p>
</footer>

</body>
</html>
