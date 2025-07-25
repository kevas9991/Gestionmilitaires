<?php
require_once 'auth.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM missions WHERE id = ?");
$stmt->execute([$id]);
$mission = $stmt->fetch();

$stmt2 = $pdo->prepare("SELECT nom FROM militaires m JOIN mission_militaires mm ON m.id = mm.militaire_id WHERE mm.mission_id = ?");
$stmt2->execute([$id]);
$militaires = $stmt2->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression mission</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f4f6fa; }
        .print-box {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.10);
            padding: 40px 32px;
        }
        .print-title {
            text-align: center;
            font-size: 2.3em;
            font-weight: bold;
            margin-bottom: 32px;
        }
        .print-info {
            font-size: 1.2em;
            margin-bottom: 18px;
        }
        .print-label {
            font-weight: bold;
            color: #3a50b2;
        }
        .btn-print, .btn-retour {
            display: inline-block;
            margin: 24px 12px 0 0;
            padding: 10px 28px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn-print:hover, .btn-retour:hover {
            background: #0056b3;
        }
        @media print {
            .btn-print, .btn-retour { display: none; }
            .print-box { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="print-box">
        <div class="print-title"><?= htmlspecialchars($mission['nom']) ?></div>
        <div class="print-info">
            <span class="print-label">Description :</span>
            <?= htmlspecialchars($mission['description']) ?>
        </div>
        <div class="print-info">
            <span class="print-label">Statut :</span>
            <?= htmlspecialchars($mission['statut']) ?>
        </div>
        <div class="print-info">
            <span class="print-label">Militaires assignés :</span>
            <?= implode(', ', $militaires) ?>
        </div>
        <button class="btn-print" onclick="window.print()">Imprimer</button>
        <a href="missions.php" class="btn-retour">Retour à la liste des missions</a>
    </div>
</body>
</html>