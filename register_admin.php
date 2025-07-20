<?php
// Démarre la session
session_start();

// Paramètres de connexion à la base de données
$host = 'localhost';
$dbname = 'gdm_db';
$user = 'root';
$pass = '';

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Affiche une erreur si la connexion échoue
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Variables pour les messages d'erreur et de succès
$error = '';
$success = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie les données du formulaire
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Vérifie le nombre d'admins existants
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $nb_admins = $stmt->fetchColumn();

    // Si le nombre max d'admins est atteint
    if ($nb_admins >= 3) {
        $error = "Nombre maximum d'administrateurs atteint (3).";
    } elseif (strlen($username) < 3 || strlen($password) < 3) {
        // Vérifie la longueur minimale des champs
        $error = "Nom d'utilisateur et mot de passe doivent contenir au moins 3 caractères.";
    } else {
        // Vérifie si le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Ce nom d'utilisateur existe déjà.";
        } else {
            // Hash le mot de passe et insère le nouvel admin
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = "Inscription réussie ! Vous pouvez vous connecter.";
        }
    }
}
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Inscription Administrateur</h2>
        <!-- Affiche le message d'erreur si besoin -->
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <!-- Affiche le message de succès si besoin -->
        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <!-- Formulaire d'inscription admin -->
        <form method="post">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">S'inscrire</button>
        </form>
        <!-- Lien retour vers la page de connexion -->
        <p style="text-align:center;margin-top:15px;">
            <a href="login.php">Retour à la connexion</a>
        </p>
    </div>
</body>
</html>