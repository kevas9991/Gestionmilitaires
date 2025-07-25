<?php
require_once 'auth.php';
require_once 'db.php';

// Récupérer la liste des militaires
$stmt = $pdo->query("SELECT id, nom, grade FROM militaires");
$militaires = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $chef_id = $_POST['chef'];
    $soldats_selectionnes = $_POST['soldats'] ?? [];

    // Insérer la mission
    $stmt = $pdo->prepare("INSERT INTO missions (nom, description, statut) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $description, $statut]);
    $mission_id = $pdo->lastInsertId();

    // Associer le chef
    $stmt2 = $pdo->prepare("INSERT INTO mission_militaires (mission_id, militaire_id) VALUES (?, ?)");
    $stmt2->execute([$mission_id, $chef_id]);

    // Associer les soldats (sauf le chef)
    foreach ($soldats_selectionnes as $soldat_id) {
        if ($soldat_id != $chef_id) {
            $stmt2->execute([$mission_id, $soldat_id]);
        }
    }

    header('Location: missions.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une mission</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-box {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 32px;
        }
        .form-box h2 {
            text-align: center;
            margin-bottom: 24px;
        }
        .form-box label {
            display: block;
            margin-top: 16px;
            margin-bottom: 6px;
        }
        .form-box input, .form-box textarea, .form-box select, .form-box button {
            width: 100%;
            margin-bottom: 12px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .form-box button {
            background: #007bff;
            color: #fff;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
        }
        .form-box button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-logo">GDM</a>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="gestion_personnel.php">Gestion du personnel</a>
            <a href="missions.php" style="color: #FFD700;">Missions</a>
        </div>
    </nav>
    <div class="form-box">
        <h2>Créer une mission</h2>
        <form method="POST">
            <label for="nom">Nom de la mission</label>
            <input type="text" name="nom" id="nom" required>

            <label for="description">Description</label>
            <textarea name="description" id="description"></textarea>

            <label for="statut">Statut</label>
            <select name="statut" id="statut">
                <option value="En cours">En cours</option>
                <option value="Terminée">Terminée</option>
            </select>

            <label for="chef">Chef de mission (haut grade)</label>
            <select name="chef" id="chef" required>
                <option value="">-- Sélectionner le chef --</option>
                <?php foreach ($militaires as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom']) ?> (<?= htmlspecialchars($m['grade']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="soldats">Soldats accompagnants</label>
            <select name="soldats[]" id="soldats" multiple size="5">
                <?php foreach ($militaires as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom']) ?> (<?= htmlspecialchars($m['grade']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Créer</button>
        </form>
    </div>
</body>
</html>