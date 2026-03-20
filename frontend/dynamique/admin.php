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

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    setFlash("Accès refusé");
    header('Location: auth.php');
    exit();
}

if (isset($_POST['add_user'])) {
    $stmt = $db->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['username'], $_POST['email'], $_POST['role']]);

    setFlash("Utilisateur ajouté");
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_user'])) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_user']]);

    setFlash("Utilisateur supprimé");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['update_user'])) {
    $stmt = $db->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->execute([
        $_POST['username'],
        $_POST['email'],
        $_POST['role'],
        $_POST['id']
    ]);

    setFlash("Utilisateur mis à jour");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['add_game'])) {
    $stmt = $db->prepare("INSERT INTO games (name, success_count, player_count) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['success_count'],
        $_POST['player_count']
    ]);

    setFlash("Jeu ajouté");
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_game'])) {
    $stmt = $db->prepare("DELETE FROM games WHERE id = ?");
    $stmt->execute([$_GET['delete_game']]);

    setFlash("Jeu supprimé");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['update_game'])) {
    $stmt = $db->prepare("UPDATE games SET name=?, success_count=?, player_count=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['success_count'],
        $_POST['player_count'],
        $_POST['id']
    ]);

    setFlash("Jeu mis à jour");
    header("Location: admin.php");
    exit();
}

$users = $db->query("SELECT id, username, email, role FROM users ORDER BY id ASC")->fetchAll();
$games = $db->query("SELECT id, name, success_count, player_count FROM games ORDER BY id ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Aetheria - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <header>
        <h2>Aetheria - Admin</h2>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <?php showFlash(); ?>

    <h1>Panel d’administration</h1>

    <section>

        <h2>Gestion des utilisateurs</h2>

        <?php if (empty($users)): ?>
            <p>Aucun utilisateur</p>
        <?php else: ?>

            <?php foreach ($users as $user): ?>
                <form method="POST">

                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    <input type="text" name="role" value="<?= htmlspecialchars($user['role']) ?>">

                    <button type="submit" name="update_user">Enregistrer</button>

                    <a href="admin.php?delete_user=<?= $user['id'] ?>"
                        onclick="return confirm('Supprimer cet utilisateur ?')">
                        Supprimer
                    </a>

                </form>
                <hr>
            <?php endforeach; ?>

        <?php endif; ?>

        <h3>Ajouter un utilisateur</h3>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="role" placeholder="Role" required>

            <button type="submit" name="add_user">Ajouter</button>
        </form>

    </section>

    <section>

        <h2>Gestion des jeux</h2>

        <?php if (empty($games)): ?>
            <p>Aucun jeu</p>
        <?php else: ?>

            <?php foreach ($games as $game): ?>
                <form method="POST">

                    <input type="hidden" name="id" value="<?= $game['id'] ?>">

                    <input type="text" name="name" value="<?= htmlspecialchars($game['name']) ?>">
                    <input type="number" name="success_count" value="<?= htmlspecialchars($game['success_count']) ?>">
                    <input type="number" name="player_count" value="<?= htmlspecialchars($game['player_count']) ?>">

                    <button type="submit" name="update_game">Enregistrer</button>

                    <a href="admin.php?delete_game=<?= $game['id'] ?>"
                        onclick="return confirm('Supprimer ce jeu ?')">
                        Supprimer
                    </a>

                </form>
                <hr>
            <?php endforeach; ?>

        <?php endif; ?>

        <h3>Ajouter un jeu</h3>

        <form method="POST">
            <input type="text" name="name" placeholder="Nom du jeu" required>
            <input type="number" name="success_count" placeholder="Succès" required>
            <input type="number" name="player_count" placeholder="Joueurs" required>

            <button type="submit" name="add_game">Ajouter</button>
        </form>

    </section>

    <footer>
        <p>2026 - Projet Étudiants</p>
    </footer>

</body>

</html>