<?php
session_start();
require_once('backend/config/db.php');

$db = Database::connect();

$message = "";

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

if (isset($_POST['action']) && $_POST['action'] === 'login') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        setFlash("Connexion réussie");

        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $message = "Email ou mot de passe incorrect";
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'register') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $db->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute(['email' => $email]);

    if ($check->fetch()) {
        $message = "Cet email est déjà utilisé";
    } else {

        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, role) 
            VALUES (:username, :email, :password, 'user')
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

        setFlash("Compte créé avec succès, vous pouvez vous connecter");

        header("Location: auth.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <header>
        <h2>Aetheria</h2>

        <nav>
            <a href="index.php">Accueil</a>
        </nav>
    </header>

    <?php showFlash(); ?>

    <?php if (!empty($message)): ?>
        <div><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <section>

        <h2>Connexion</h2>

        <form method="POST">

            <input type="hidden" name="action" value="login">

            <div>
                <label>Email :</label><br>
                <input type="email" name="email" required>
            </div>

            <br>

            <div>
                <label>Mot de passe :</label><br>
                <input type="password" name="password" required>
            </div>

            <br>

            <button type="submit">Se connecter</button>

        </form>

    </section>

    <hr>

    <section>

        <h2>Inscription</h2>

        <form method="POST">

            <input type="hidden" name="action" value="register">

            <div>
                <label>Pseudo :</label><br>
                <input type="text" name="username" required>
            </div>

            <br>

            <div>
                <label>Email :</label><br>
                <input type="email" name="email" required>
            </div>

            <br>

            <div>
                <label>Mot de passe :</label><br>
                <input type="password" name="password" required>
            </div>

            <br>

            <button type="submit">S'inscrire</button>

        </form>

    </section>

    <footer>
        <p>2026 - Projet Étudiants</p>
    </footer>

</body>

</html>