<?php
require_once 'auth.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: missions.php'); exit(); }

// Récupérer la mission
$stmt = $pdo->prepare("SELECT * FROM missions WHERE id = ?");
$stmt->execute([$id]);
$mission = $stmt->fetch();
if (!$mission) { header('Location: missions.php'); exit(); }

// Récupérer les militaires
$stmt = $pdo->query("SELECT id, nom FROM militaires");
$militaires = $stmt->fetchAll();

// Militaires déjà assignés
$stmt2 = $pdo->prepare("SELECT militaire_id FROM mission_militaires WHERE mission_id = ?");
$stmt2->execute([$id]);
$militaires_assignes = $stmt2->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $militaires_selectionnes = $_POST['militaires'] ?? [];

    // Mise à jour mission
    $stmt = $pdo->prepare("UPDATE missions SET nom=?, description=?, statut=? WHERE id=?");
    $stmt->execute([$nom, $description, $statut, $id]);

    // Mise à jour militaires assignés
    $pdo->prepare("DELETE FROM mission_militaires WHERE mission_id=?")->execute([$id]);
    foreach ($militaires_selectionnes as $militaire_id) {
        $pdo->prepare("INSERT INTO mission_militaires (mission_id, militaire_id) VALUES (?, ?)")->execute([$id, $militaire_id]);
    }

    header('Location: missions.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la mission</title>
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
        .btn-retour {
            display: block;
            margin: 0 auto 18px auto;
            background: #f5f5f5;
            color: #007bff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            width: fit-content;
        }
        .btn-retour:hover {
            background: #e0e0e0;
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Modifier la mission</h2>
        <a href="missions.php" class="btn-retour">← Retour à la liste des missions</a>
        <form method="POST">
            <label for="nom">Nom de la mission</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($mission['nom']) ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description"><?= htmlspecialchars($mission['description']) ?></textarea>

            <label for="statut">Statut</label>
            <select name="statut" id="statut">
                <option value="En cours" <?= $mission['statut']=='En cours'?'selected':'' ?>>En cours</option>
                <option value="Terminée" <?= $mission['statut']=='Terminée'?'selected':'' ?>>Terminée</option>
            </select>

            <label for="militaires">Militaires à assigner :</label>
            <select name="militaires[]" id="militaires" multiple size="5">
                <?php foreach ($militaires as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= in_array($m['id'], $militaires_assignes)?'selected':'' ?>>
                        <?= htmlspecialchars($m['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>