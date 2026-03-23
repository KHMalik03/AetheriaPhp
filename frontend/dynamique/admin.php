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
        echo "<div class='flash'>" . htmlspecialchars($_SESSION['flash']) . "</div>";
        unset($_SESSION['flash']);
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    setFlash("Accès refusé");
    header('Location: auth.php');
    exit();
}

if (isset($_POST['add_user'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $db->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)")
       ->execute([$_POST['username'], $_POST['email'], $password, $_POST['role']]);

    setFlash("Utilisateur ajouté");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['add_game'])) {
    $db->prepare("INSERT INTO games (name,type,description,image_url,release_date,studio) VALUES (?,?,?,?,?,?)")
       ->execute([
           $_POST['name'],
           $_POST['type'],
           $_POST['description'],
           $_POST['image_url'],
           $_POST['release_date'],
           $_POST['studio']
       ]);

    setFlash("Jeu ajouté");
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_user'])) {
    $db->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['delete_user']]);
    header("Location: admin.php"); exit();
}

if (isset($_GET['delete_game'])) {
    $db->prepare("DELETE FROM games WHERE id=?")->execute([$_GET['delete_game']]);
    header("Location: admin.php"); exit();
}

$users = $db->query("SELECT * FROM users")->fetchAll();
$games = $db->query("SELECT * FROM games")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin</title>
<link rel="stylesheet" href="../../frontend/statics/admin.css">
</head>

<body>

<header>
    <div class="logo">
        <img src="../../Images/logo.png">
        <strong>Aetheria</strong>
    </div>
    <nav>
        <a href="../../index.php">Accueil</a>
        <a href="../../backend/auth/logout.php">Déconnexion</a>
    </nav>
</header>

<?php showFlash(); ?>

<div class="admin-container">

<h1 class="title">Panel d’administration</h1>

<div class="admin-card">

<div class="table-area">
<table>
<tr><th>User</th><th>Email</th><th>Role</th></tr>

<?php foreach($users as $u): ?>
<tr>
<td><?= $u['username'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['role'] ?></td>
</tr>
<?php endforeach; ?>

</table>
</div>

<div class="action-area">
<button class="add" onclick="openModal('userModal')">Rajouter User</button>

<?php foreach($users as $u): ?>
<a class="delete" href="?delete_user=<?= $u['id'] ?>">Supp</a>
<?php endforeach; ?>
</div>

</div>

<div class="admin-card">

<div class="table-area">
<table>
<tr><th>Game</th><th>Type</th><th>Studio</th></tr>

<?php foreach($games as $g): ?>
<tr>
<td><?= $g['name'] ?></td>
<td><?= $g['type'] ?></td>
<td><?= $g['studio'] ?></td>
</tr>
<?php endforeach; ?>

</table>
</div>

<div class="action-area">
<button class="add" onclick="openModal('gameModal')">Rajouter Game</button>

<?php foreach($games as $g): ?>
<a class="delete" href="?delete_game=<?= $g['id'] ?>">Supp</a>
<?php endforeach; ?>
</div>

</div>

</div>

<div id="userModal" class="modal">
<div class="modal-content">
<span class="close" onclick="closeModal('userModal')">X</span>
<form method="POST">
<input name="username" placeholder="Username" required>
<input name="email" placeholder="Email" required>
<input name="password" placeholder="Password" required>
<input name="role" placeholder="Role" required>
<button class="add" name="add_user">Ajouter</button>
</form>
</div>
</div>

<div id="gameModal" class="modal">
<div class="modal-content">
<span class="close" onclick="closeModal('gameModal')">X</span>
<form method="POST">
<input name="name" placeholder="Nom" required>
<input name="type" placeholder="Type" required>
<input name="description" placeholder="Description" required>
<input name="image_url" placeholder="Image" required>
<input type="date" name="release_date" required>
<input name="studio" placeholder="Studio" required>
<button class="add" name="add_game">Ajouter</button>
</form>
</div>
</div>

<script>
function openModal(id){document.getElementById(id).style.display="flex";}
function closeModal(id){document.getElementById(id).style.display="none";}
</script>

<footer>
<p>2026 - Projet Étudiants</p>
</footer>

</body>
</html>