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

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère le nom d'utilisateur et le mot de passe saisis
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prépare et exécute la requête pour récupérer le mot de passe hashé de l'admin
    $stmt = $pdo->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Vérifie le mot de passe
    if ($admin && password_verify($password, $admin['password'])) {
        // Authentification réussie, crée la session admin
        $_SESSION['admin'] = true;
        header('Location: dashboard.php'); // Redirige vers le tableau de bord
        exit();
    } else {
        // Identifiants incorrects, affiche une erreur
        $error = "Identifiants incorrects.";
    }
}
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-split">
        <div class="login-image"></div>
        <div class="login-form-side">
            <div class="login-container">
                <h2>Connexion Administrateur</h2>
                <!-- Affiche le message d'erreur si besoin -->
                <?php if (!empty($error)): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
                <!-- Formulaire de connexion admin -->
                <form method="post">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" name="username" id="username" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" name="password" id="password" required>
                    <button type="submit">Se connecter</button>
                </form>
                <!-- Lien vers la page d'inscription admin -->
                <p style="text-align:center;margin-top:15px;">
                    <a href="register_admin.php">S'inscrire comme admin</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>