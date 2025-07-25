<?php
require_once 'auth.php'; // Vérifie l'authentification admin et démarre la session
require_once 'db.php';   // Connexion à la base de données

// Récupère le nombre total de militaires
$stmt = $pdo->query("SELECT COUNT(*) FROM militaires");
$total_militaires = $stmt->fetchColumn();

// Pagination
$limit = 10; // <-- Mets 10 ici
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Récupère la liste paginée des militaires
$stmt = $pdo->prepare("SELECT id, nom, grade, photo FROM militaires ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$militaires = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <!-- Logo GDM cliquable pour retourner au dashboard -->
        <a href="dashboard.php" class="navbar-logo" style="text-decoration:none;color:inherit;">GDM</a>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="gestion_personnel.php">Gestion du personnel</a>
            <a href="missions.php">Missions</a>
        </div>
    </nav>
    <div class="main-content">
        <!-- Carte affichant le total de militaires -->
        <div class="card-total">
            <h3>Total Militaires</h3>
            <div class="total-number"><?= $total_militaires ?></div>
        </div>
        <h2 class="section-title">Militaires en service</h2>
        <div class="militaires-list">
            <!-- Boucle sur chaque militaire pour afficher sa carte -->
            <?php foreach ($militaires as $militaire): ?>
                <div class="militaire-card" onclick="afficherPopup(<?= htmlspecialchars(json_encode($militaire)) ?>)">
                    <?php if (!empty($militaire['photo']) && file_exists($militaire['photo'])): ?>
                        <!-- Affiche la photo du militaire si elle existe -->
                        <img src="<?= htmlspecialchars($militaire['photo']) ?>" alt="Photo" class="militaire-photo" />
                    <?php else: ?>
                        <!-- Affiche un espace réservé si pas de photo -->
                        <div class="militaire-photo">100 × 100</div>
                    <?php endif; ?>
                    <!-- Affiche le nom et le grade du militaire -->
                    <div class="militaire-nom"><?= htmlspecialchars($militaire['nom']) ?></div>
                    <div class="militaire-grade">Grade: <?= htmlspecialchars($militaire['grade']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $nb_pages = ceil($total_militaires / $limit);
        ?>
        <div class="pagination" style="text-align:center;margin:30px 0;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn-page">← Page précédente</a>
            <?php endif; ?>
            <span style="margin:0 12px;">Page <?= $page ?> / <?= $nb_pages ?></span>
            <?php if ($page < $nb_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn-page">Page suivante →</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Pop-up affichant les infos détaillées du militaire sélectionné -->
    <div id="militaire-popup" class="militaire-popup" style="display:none;">
        <div class="popup-content">
            <!-- Bouton pour fermer le pop-up -->
            <span class="close-btn" onclick="fermerPopup()">&times;</span>
            <!-- Photo du militaire -->
            <img id="popup-photo" src="" alt="Photo" style="width:80px;height:80px;border-radius:50%;margin-bottom:10px;">
            <!-- Infos du militaire (remplies dynamiquement en JS) -->
            <div id="popup-nom" class="popup-info"></div>
            <div id="popup-grade" class="popup-info"></div>
            <div id="popup-unite" class="popup-info"></div>
            <div id="popup-matricule" class="popup-info"></div>
            <div id="popup-sexe" class="popup-info"></div>
            <div id="popup-etat" class="popup-info"></div>
            <div id="popup-naissance" class="popup-info"></div>
            <div id="popup-lieu" class="popup-info"></div>
            <div id="popup-nationalite" class="popup-info"></div>
            <div id="popup-telephone" class="popup-info"></div>
            <div id="popup-email" class="popup-info"></div>
            <div id="popup-enrolement" class="popup-info"></div>
        </div>
    </div>
    <!-- Script JS pour gérer l'affichage et la fermeture du pop-up -->
    <script>
function afficherPopup(data) {
    document.getElementById('militaire-popup').style.display = 'flex';
    document.getElementById('popup-photo').src = data.photo && data.photo !== "" ? data.photo : "";
    document.getElementById('popup-nom').innerHTML = "<strong>Nom :</strong> " + (data.nom || "");
    document.getElementById('popup-grade').innerHTML = "<strong>Grade :</strong> " + (data.grade || "");
    document.getElementById('popup-unite').innerHTML = "<strong>Unité :</strong> " + (data.unite || "");
    document.getElementById('popup-matricule').innerHTML = "<strong>Matricule :</strong> " + (data.matricule || "");
    document.getElementById('popup-sexe').innerHTML = "<strong>Sexe :</strong> " + (data.sexe || "");
    document.getElementById('popup-etat').innerHTML = "<strong>État civil :</strong> " + (data.etat_civil || "");
    document.getElementById('popup-naissance').innerHTML = "<strong>Date de naissance :</strong> " + (data.date_naissance || "");
    document.getElementById('popup-lieu').innerHTML = "<strong>Lieu de naissance :</strong> " + (data.lieu_naissance || "");
    document.getElementById('popup-nationalite').innerHTML = "<strong>Nationalité :</strong> " + (data.nationalite || "");
    document.getElementById('popup-telephone').innerHTML = "<strong>Téléphone :</strong> " + (data.telephone || "");
    document.getElementById('popup-email').innerHTML = "<strong>Email :</strong> " + (data.email || "");
    document.getElementById('popup-enrolement').innerHTML = "<strong>Date d'enrôlement :</strong> " + (data.date_enrolement || "");
}
function fermerPopup() {
    document.getElementById('militaire-popup').style.display = 'none';
}
</script>
<a href="logout.php" class="btn-deconnexion">Déconnecter</a>
</body>
</html>