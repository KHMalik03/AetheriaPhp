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
        echo "<div class='flash'>" . htmlspecialchars($_SESSION['flash']) . "</div>";
        unset($_SESSION['flash']);
    }
}

$meRes = apiCall('GET','/me');
if ($meRes['code'] !== 200 || ($meRes['body']['role'] ?? '') !== 'admin') {
    header('Location: /AetheriaPhp/frontend/dynamique/auth.php');
    exit();
}

if (isset($_POST['add_user'])) {
    $result = apiCall('POST','/users', [
        'username' => $_POST['username'],
        'email'    => $_POST['email'],
        'password' => $_POST['password'],
    ]);
    setFlash($result['code'] === 201 ? "Utilisateur ajouté" : ($result['body']['message'] ?? "Erreur"));
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_user'])) {
    apiCall('DELETE','/users/' . (int)$_GET['delete_user']);
    setFlash("Utilisateur supprimé");
    header("Location: admin.php");
    exit();
}

if (isset($_POST['add_game'])) {
    $result = apiCall('POST','/games', [
        'name'         => $_POST['name'],
        'type'         => $_POST['type'],
        'description'  => $_POST['description'],
        'image_url'    => $_POST['image_url'],
        'release_date' => $_POST['release_date'],
        'studio'       => $_POST['studio'],
    ]);
    setFlash($result['code'] === 201 ? "Jeu ajouté" : ($result['body']['message'] ?? "Erreur"));
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_game'])) {
    apiCall('DELETE','/games/' . (int)$_GET['delete_game']);
    setFlash("Jeu supprimé");
    header("Location: admin.php");
    exit();
}

$usersRes = apiCall('GET','/users');
$users    = $usersRes['body'] ?? [];

$gamesRes = apiCall('GET','/games');
$games    = $gamesRes['body'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="/AetheriaPhp/frontend/statics/admin.css">
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

<h1 class="title">Panel d'administration</h1>

<div class="admin-card">
<div class="table-area">
<table>
<tr><th>User</th><th>Email</th><th>Role</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
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
<td><?= htmlspecialchars($g['name']) ?></td>
<td><?= htmlspecialchars($g['type'] ?? '') ?></td>
<td><?= htmlspecialchars($g['studio'] ?? '') ?></td>
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
<input type="password" name="password" placeholder="Password" required>
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
<input name="image_url" placeholder="Image URL" required>
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
