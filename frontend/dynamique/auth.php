<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aetheria - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="auth.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="Images/logo.png" alt="logo">
        Aetheria
    </div>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="auth.php">Connexion/Inscription</a>
    </nav>
</header>

<section class="container">

    <div class="form-box">

        <input type="radio" name="tab" id="login" checked>
        <input type="radio" name="tab" id="register">

        <div class="tabs">
            <label for="login" class="tab">Connexions</label>
            <label for="register" class="tab">Inscriptions</label>
        </div>

        <div class="form login-form">
            <label>E-Mail :</label>
            <input type="email" required>

            <label>Mot de passe :</label>
            <input type="password" required>

            <button class="main-btn">Connexions</button>
        </div>

        <div class="form register-form">
            <label>Pseudo :</label>
            <input type="text" required>

            <label>E-Mail :</label>
            <input type="email" required>

            <label>Mot de passe :</label>
            <input type="password" required>

            <button class="main-btn">Inscriptions</button>
        </div>

    </div>

</section>

<footer>
    2026 - Project Etudiants
</footer>

</body>
</html>