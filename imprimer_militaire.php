<?php
// Démarre la session et vérifie que l'utilisateur est admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    exit("Accès refusé."); // Bloque l'accès si non admin
}

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
    die("Erreur : " . $e->getMessage()); // Affiche une erreur si la connexion échoue
}

// Récupère l'ID du militaire à imprimer
$id = intval($_GET['id'] ?? 0);
// Prépare et exécute la requête pour récupérer le militaire
$stmt = $pdo->prepare("SELECT * FROM militaires WHERE id = ?");
$stmt->execute([$id]);
$militaire = $stmt->fetch();

// Si aucun militaire trouvé, affiche un message et arrête le script
if (!$militaire) {
    exit("Militaire introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression fiche militaire</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style de la fiche à imprimer */
        .fiche-print { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);}
        .fiche-print img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 18px;}
        .fiche-print h2 { margin-bottom: 10px; }
        .fiche-print .info { margin-bottom: 8px; }
        @media print {
            .btn-print { display: none; } /* Cache le bouton imprimer à l'impression */
        }
    </style>
</head>
<body>
    <div class="fiche-print">
        <?php if (!empty($militaire['photo']) && file_exists($militaire['photo'])): ?>
            <!-- Affiche la photo du militaire si elle existe -->
            <img src="<?= htmlspecialchars($militaire['photo']) ?>" alt="Photo">
        <?php endif; ?>
        <!-- Affiche le nom et le prénom du militaire -->
        <h2><?= htmlspecialchars($militaire['nom']) ?> <?= htmlspecialchars($militaire['prenom']) ?></h2>
        <!-- Affiche les différentes informations du militaire -->
        <div class="info"><strong>Matricule :</strong> <?= htmlspecialchars($militaire['matricule']) ?></div>
        <div class="info"><strong>Grade :</strong> <?= htmlspecialchars($militaire['grade']) ?></div>
        <div class="info"><strong>Unité :</strong> <?= htmlspecialchars($militaire['unite']) ?></div>
        <div class="info"><strong>Sexe :</strong> <?= htmlspecialchars($militaire['sexe']) ?></div>
        <div class="info"><strong>État civil :</strong> <?= htmlspecialchars($militaire['etat_civil']) ?></div>
        <div class="info"><strong>Date de naissance :</strong> <?= htmlspecialchars($militaire['date_naissance']) ?></div>
        <div class="info"><strong>Lieu de naissance :</strong> <?= htmlspecialchars($militaire['lieu_naissance']) ?></div>
        <div class="info"><strong>Nationalité :</strong> <?= htmlspecialchars($militaire['nationalite']) ?></div>
        <div class="info"><strong>Téléphone :</strong> <?= htmlspecialchars($militaire['telephone']) ?></div>
        <div class="info"><strong>Email :</strong> <?= htmlspecialchars($militaire['email']) ?></div>
        <div class="info"><strong>Date d'enrôlement :</strong> <?= htmlspecialchars($militaire['date_enrolement']) ?></div>
        <!-- Bouton pour imprimer la fiche -->
        <button class="btn-print" onclick="window.print()">Imprimer</button>
    </div>
</body>
</html>
