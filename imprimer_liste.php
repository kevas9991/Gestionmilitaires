<?php
// Démarre la session et vérifie que l'utilisateur est admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    exit("Accès refusé.");
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
    die("Erreur : " . $e->getMessage());
}

// Récupère tous les militaires pour l'impression
$stmt = $pdo->query("SELECT * FROM militaires ORDER BY id DESC");
$militaires = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Imprimer la liste des militaires</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Conteneur principal de la liste à imprimer */
        .table-print-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 30px 20px;
        }
        /* Table compacte */
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        /* Style des cellules et en-têtes */
        .table-print th, .table-print td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
            font-size: 1rem;
        }
        .table-print th {
            background: #4054b2;
            color: #fff;
            font-weight: 600;
        }
        /* Style des photos */
        .table-print img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        /* Masque les boutons à l'impression */
        @media print {
            .btn-print, .btn-retour { display: none; }
            .table-print-container { box-shadow: none; padding: 0; }
        }
        /* Bouton imprimer vert */
        .btn-print {
            margin: 30px auto 0 auto;
            display: block;
            padding: 12px 30px;
            background: #2ecc40;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            width: max-content;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #27ae60;
        }
        /* Bouton retour bleu */
        .btn-retour {
            margin: 20px auto;
            display: block;
            padding: 12px 30px;
            background: #283a7a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            width: max-content;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="table-print-container">
        <!-- Titre de la liste -->
        <h2 style="text-align:center;margin-top:10px;">Liste des militaires</h2>
        <table class="table-print">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Grade</th>
                    <th>Unité</th>
                    <th>Date d'enrôlement</th>
                </tr>
            </thead>
            <tbody>
                <!-- Boucle sur chaque militaire pour afficher sa ligne -->
                <?php foreach ($militaires as $militaire): ?>
                <tr>
                    <td>
                        <?php if (!empty($militaire['photo']) && file_exists($militaire['photo'])): ?>
                            <!-- Affiche la photo du militaire si elle existe -->
                            <img src="<?= htmlspecialchars($militaire['photo']) ?>" alt="Photo">
                        <?php else: ?>
                            <!-- Affiche un espace réservé si pas de photo -->
                            <div style="width:40px;height:40px;background:#e0e0e0;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#aaa;">?</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($militaire['nom']) ?></td>
                    <td><?= htmlspecialchars($militaire['grade']) ?></td>
                    <td><?= htmlspecialchars($militaire['unite'] ?? '-') ?></td>
                    <td><?= !empty($militaire['date_enrolement']) ? date('d/m/Y', strtotime($militaire['date_enrolement'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Bouton pour imprimer la liste -->
        <button class="btn-print" onclick="window.print()">Imprimer</button>
        <!-- Bouton pour retourner à la liste du personnel -->
        <a href="gestion_personnel.php" class="btn-retour">← Retour à la liste</a>
    </div>
</body>
</html>