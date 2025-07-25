<?php
require_once 'auth.php';
require_once 'db.php';

// Récupérer la liste des missions
$stmt = $pdo->query("SELECT * FROM missions ORDER BY id DESC");
$missions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des missions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .main-content {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 32px;
        }
        h2.section-title {
            text-align: center;
            margin-bottom: 24px;
        }
        .missions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .missions-table th, .missions-table td {
            border: 1px solid #e0e0e0;
            padding: 12px 8px;
            text-align: left;
        }
        .missions-table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .missions-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .btn {
            display: inline-block;
            margin-bottom: 16px;
            background: #007bff;
            color: #fff;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
        }
        .action-link {
            color: #007bff;
            text-decoration: underline;
            font-weight: bold;
        }
        .action-link:hover {
            color: #0056b3;
        }
        .action-btn {
            display: inline-block;
            color: #007bff;
            text-decoration: none;
            margin-right: 8px;
        }
        .action-btn:hover {
            color: #0056b3;
        }
        .fa-solid {
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-logo">GDM</a>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="missions.php" style="color: #FFD700;">Missions</a>
        </div>
    </nav>
    <div class="main-content">
        <h2 class="section-title">Liste des missions</h2>
        <a href="ajouter_mission.php" class="btn">Créer une mission</a>
        <table class="missions-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Militaires assignés</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missions as $mission): ?>
                <tr>
                    <td><?= htmlspecialchars($mission['nom']) ?></td>
                    <td><?= htmlspecialchars($mission['description']) ?></td>
                    <td><?= htmlspecialchars($mission['statut']) ?></td>
                    <td>
                        <?php
                        $stmt2 = $pdo->prepare("SELECT nom FROM militaires m JOIN mission_militaires mm ON m.id = mm.militaire_id WHERE mm.mission_id = ?");
                        $stmt2->execute([$mission['id']]);
                        $noms = $stmt2->fetchAll(PDO::FETCH_COLUMN);
                        echo implode(', ', $noms);
                        ?>
                    </td>
                    <td>
                        <div class="actions-group">
                            <a href="modifier_mission.php?id=<?= $mission['id'] ?>" title="Modifier" class="action-btn modif">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="imprimer_mission.php?id=<?= $mission['id'] ?>" title="Imprimer" class="action-btn print" target="_blank">
                                <i class="fa-solid fa-print"></i>
                            </a>
                            <a href="supprimer_mission.php?id=<?= $mission['id'] ?>" title="Supprimer" class="action-btn suppr" onclick="return confirm('Voulez-vous vraiment supprimer cette mission ?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>