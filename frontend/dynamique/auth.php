<?php
session_start();
require_once('../../backend/config/db.php');

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

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Tous les champs sont requis";
    } else {

        $stmt = $db->prepare("
            SELECT id, username, email, password, role 
            FROM users 
            WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            setFlash("Connexion réussie");

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: ../../index.php');
            }
            exit();

        } else {
            $message = "Email ou mot de passe incorrect";
        }
    }
}


if (isset($_POST['action']) && $_POST['action'] === 'register') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    if (empty($username) || empty($email) || empty($passwordRaw)) {
        $message = "Tous les champs sont requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide";
    } else {

        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

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
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../frontend/statics/auth.css">
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
