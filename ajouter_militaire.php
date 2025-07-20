<?php
// Démarre la session pour vérifier l'authentification
session_start();
// Vérifie si l'utilisateur est admin, sinon redirige vers la page de login
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
require_once 'auth.php';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    // Génère automatiquement le matricule
    $matricule = 'MIL' . date('Ymd') . rand(1000,9999);
    $sexe = $_POST['sexe'] ?? '';
    $etat_civil = $_POST['etat_civil'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    // Définit "Congolaise" comme nationalité par défaut
    $nationalite = trim($_POST['nationalite'] ?? 'Congolaise');
    $unite = $_POST['unite'] ?? '';
    $grade = $_POST['grade'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date_enrolement = $_POST['date_enrolement'] ?? '';
    $photo = '';

    // Vérifie que le militaire a au moins 18 ans
    if ($date_naissance) {
        $date18 = date('Y-m-d', strtotime('-18 years'));
        if ($date_naissance > $date18) {
            $error = "Le militaire doit avoir au moins 18 ans.";
        }
    }

    // Gère l'upload de la photo si elle est envoyée
    if (empty($error) && isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        // Crée le dossier uploads s'il n'existe pas
        if (!is_dir('uploads')) mkdir('uploads');
        // Génère un nom unique pour la photo et la déplace dans uploads
        $photo_name = uniqid() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_name);
        $photo = 'uploads/' . $photo_name;
    }

    // Vérifie que les champs obligatoires sont remplis avant d'insérer
    if (empty($error) && $nom && $grade && $unite) {
        // Prépare et exécute la requête d'insertion du militaire
        $stmt = $pdo->prepare("INSERT INTO militaires (nom, prenom, matricule, sexe, etat_civil, date_naissance, lieu_naissance, nationalite, grade, unite, telephone, email, photo, date_enrolement) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $matricule, $sexe, $etat_civil, $date_naissance, $lieu_naissance, $nationalite, $grade, $unite, $telephone, $email, $photo, $date_enrolement]);
        // Redirige vers la liste avec un message de succès
        header("Location: gestion_personnel.php?msg=ajoute");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un militaire</title>
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
        <h2 class="section-title">Ajouter un militaire</h2>
        <!-- Affiche le message d'erreur si besoin -->
        <?php if (!empty($error)): ?>
            <div class="popup-message" style="background:#d32f2f;color:#fff;">
                <?= htmlspecialchars($error) ?>
            </div>
            <script>
                // Cache le message d'erreur après 2,5 secondes
                setTimeout(function() {
                    document.querySelector('.popup-message').style.display = 'none';
                }, 2500);
            </script>
        <?php endif; ?>
        <!-- Formulaire d'ajout d'un militaire -->
        <form method="post" enctype="multipart/form-data" class="add-form">
            <label for="nom">Nom complet *</label>
            <input type="text" name="nom" id="nom" required>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom">
            <label for="sexe">Sexe</label>
            <select name="sexe" id="sexe">
                <option value="">-- Sélectionner --</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>
            <label for="etat_civil">État civil</label>
            <select name="etat_civil" id="etat_civil">
                <option value="">-- Sélectionner --</option>
                <option value="Célibataire">Célibataire</option>
                <option value="Marié(e)">Marié(e)</option>
                <option value="Divorcé(e)">Divorcé(e)</option>
                <option value="Veuf(ve)">Veuf(ve)</option>
            </select>
            <label for="date_naissance">Date de naissance</label>
            <input type="date" name="date_naissance" id="date_naissance">
            <label for="lieu_naissance">Lieu de naissance (Ville, Pays)</label>
            <input type="text" name="lieu_naissance" id="lieu_naissance">
            <label for="nationalite">Nationalité</label>
            <input type="text" name="nationalite" id="nationalite" value="Congolaise">
            <label for="unite">Unité</label>
            <select name="unite" id="unite" required>
                <option value="">-- Sélectionner --</option>
                <option value="Infanterie">Infanterie</option>
                <option value="Forces Spéciales">Forces Spéciales</option>
                <option value="Marine">Marine</option>
                <option value="Armée de l'Air">Armée de l'Air</option>
                <option value="Troupes Blindées">Troupes Blindées</option>
            </select>
            <label for="grade">Grade *</label>
            <select name="grade" id="grade" required>
                <option value="">-- Sélectionner --</option>
                <option value="Capitaine">Capitaine</option>
                <option value="Lieutenant">Lieutenant</option>
                <option value="Sergent">Sergent</option>
                <option value="Colonel">Colonel</option>
                <option value="Major">Major</option>
            </select>
            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" id="telephone">
            <label for="email">Email</label>
            <input type="email" name="email" id="email">
            <label for="photo">Photo de profil</label>
            <input type="file" name="photo" id="photo" accept="image/*">
            <label for="date_enrolement">Date d'enrôlement</label>
            <input type="date" name="date_enrolement" id="date_enrolement">
            <button type="submit">Ajouter</button>
            <!-- Bouton retour vers la liste -->
            <a href="gestion_personnel.php" class="btn-retour">← Retour à la liste</a>
        </form>
    </div>
</body>
</html>