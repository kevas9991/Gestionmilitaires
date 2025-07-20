<?php
// Démarre la session et vérifie l'authentification admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php'); // Redirige si non admin
    exit();
}

require_once 'db.php';
require_once 'auth.php';

// Récupère l'ID du militaire à modifier
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: gestion_personnel.php'); // Redirige si pas d'ID
    exit();
}

// Récupère les infos du militaire
$stmt = $pdo->prepare("SELECT * FROM militaires WHERE id = ?");
$stmt->execute([$id]);
$militaire = $stmt->fetch();

if (!$militaire) {
    header('Location: gestion_personnel.php'); // Redirige si le militaire n'existe pas
    exit();
}

$message = '';
// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $sexe = $_POST['sexe'] ?? '';
    $etat_civil = $_POST['etat_civil'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $nationalite = trim($_POST['nationalite'] ?? '');
    $grade = $_POST['grade'] ?? '';
    $unite = trim($_POST['unite'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date_enrolement = $_POST['date_enrolement'] ?? '';
    $photo = $militaire['photo'];

    // Gestion de l'upload de la nouvelle photo si envoyée
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo_name = uniqid() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_name);
        $photo = 'uploads/' . $photo_name;
    }

    // Met à jour les infos du militaire dans la base
    $stmt = $pdo->prepare("UPDATE militaires SET nom=?, prenom=?, sexe=?, etat_civil=?, date_naissance=?, lieu_naissance=?, nationalite=?, grade=?, unite=?, telephone=?, email=?, photo=?, date_enrolement=? WHERE id=?");
    $stmt->execute([$nom, $prenom, $sexe, $etat_civil, $date_naissance, $lieu_naissance, $nationalite, $grade, $unite, $telephone, $email, $photo, $date_enrolement, $id]);
    $message = "Modification réussie.";
    // Recharge les données modifiées pour affichage
    $stmt = $pdo->prepare("SELECT * FROM militaires WHERE id = ?");
    $stmt->execute([$id]);
    $militaire = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un militaire</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <div class="navbar-logo">GDM</div>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="gestion_personnel.php">Gestion du personnel</a>
        </div>
    </nav>
    <div class="main-content">
        <h2 class="section-title">Modifier un militaire</h2>
        <!-- Affiche le message de succès si modification -->
        <?php if ($message): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <!-- Formulaire de modification du militaire -->
        <form method="post" enctype="multipart/form-data" class="add-form">
            <label for="nom">Nom complet *</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($militaire['nom']) ?>" required>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($militaire['prenom']) ?>">
            <label for="sexe">Sexe</label>
            <select name="sexe" id="sexe">
                <option value="">-- Sélectionner --</option>
                <option value="Homme" <?= $militaire['sexe']=='Homme'?'selected':'' ?>>Homme</option>
                <option value="Femme" <?= $militaire['sexe']=='Femme'?'selected':'' ?>>Femme</option>
            </select>
            <label for="etat_civil">État civil</label>
            <select name="etat_civil" id="etat_civil">
                <option value="">-- Sélectionner --</option>
                <option value="Célibataire" <?= $militaire['etat_civil']=='Célibataire'?'selected':'' ?>>Célibataire</option>
                <option value="Marié(e)" <?= $militaire['etat_civil']=='Marié(e)'?'selected':'' ?>>Marié(e)</option>
                <option value="Divorcé(e)" <?= $militaire['etat_civil']=='Divorcé(e)'?'selected':'' ?>>Divorcé(e)</option>
                <option value="Veuf(ve)" <?= $militaire['etat_civil']=='Veuf(ve)'?'selected':'' ?>>Veuf(ve)</option>
            </select>
            <label for="date_naissance">Date de naissance</label>
            <input type="date" name="date_naissance" id="date_naissance" value="<?= htmlspecialchars($militaire['date_naissance']) ?>">
            <label for="lieu_naissance">Lieu de naissance (Ville, Pays)</label>
            <input type="text" name="lieu_naissance" id="lieu_naissance" value="<?= htmlspecialchars($militaire['lieu_naissance']) ?>">
            <label for="nationalite">Nationalité</label>
            <input type="text" name="nationalite" id="nationalite" value="<?= htmlspecialchars($militaire['nationalite']) ?>">
            <label for="grade">Grade *</label>
            <select name="grade" id="grade" required>
                <option value="">-- Sélectionner --</option>
                <option value="Capitaine" <?= $militaire['grade']=='Capitaine'?'selected':'' ?>>Capitaine</option>
                <option value="Lieutenant" <?= $militaire['grade']=='Lieutenant'?'selected':'' ?>>Lieutenant</option>
                <option value="Sergent" <?= $militaire['grade']=='Sergent'?'selected':'' ?>>Sergent</option>
                <option value="Colonel" <?= $militaire['grade']=='Colonel'?'selected':'' ?>>Colonel</option>
                <option value="Major" <?= $militaire['grade']=='Major'?'selected':'' ?>>Major</option>
            </select>
            <label for="unite">Unité</label>
            <input type="text" name="unite" id="unite" value="<?= htmlspecialchars($militaire['unite'] ?? '') ?>">
            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($militaire['telephone']) ?>">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($militaire['email']) ?>">
            <label for="photo">Photo de profil</label>
            <input type="file" name="photo" id="photo" accept="image/*">
            <?php if (!empty($militaire['photo']) && file_exists($militaire['photo'])): ?>
                <!-- Affiche la photo actuelle du militaire -->
                <img src="<?= htmlspecialchars($militaire['photo']) ?>" alt="Photo" style="width:60px;height:60px;border-radius:50%;margin-top:10px;">
            <?php endif; ?>
            <label for="date_enrolement">Date d'enrôlement</label>
            <input type="date" name="date_enrolement" id="date_enrolement" value="<?= htmlspecialchars($militaire['date_enrolement']) ?>">
            <button type="submit">Enregistrer</button>
            <a href="gestion_personnel.php" class="btn-retour">← Retour à la liste</a>
        </form>
    </div>
</body>
</html>