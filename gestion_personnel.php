<?php
require_once 'auth.php'; // Démarre la session et vérifie l'authentification admin
require_once 'db.php';   // Connexion à la base de données

// Récupère le nombre total de militaires
$stmt = $pdo->query("SELECT COUNT(*) FROM militaires");
$total_militaires = $stmt->fetchColumn();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Nombre d'éléments par page
$offset = ($page - 1) * $limit;

// Récupère la liste des militaires avec pagination
$stmt = $pdo->prepare("SELECT * FROM militaires ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$militaires = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du personnel</title>
    <link rel="stylesheet" href="style.css">
    <!-- Icône imprimante (Font Awesome CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <!-- Ajoute un lien sur le logo GDM pour retourner au dashboard -->
        <a href="dashboard.php" class="navbar-logo" style="text-decoration:none;color:inherit;">GDM</a>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="gestion_personnel.php" style="color: #FFD700;">Gestion du personnel</a>
        </div>
    </nav>
    <div class="main-content">
        <!-- Carte affichant le total de militaires -->
        <div class="card-total" style="margin: 0 auto 40px auto;">
            <h3>Total Militaires</h3>
            <div class="total-number"><?= $total_militaires ?></div>
        </div>
        <div class="table-container">
            <h2 class="section-title">Liste des Militaires</h2>
            <table class="militaires-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Grade</th>
                        <th>Unité</th>
                        <th>Date d'enrôlement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($militaires) === 0): ?>
                    <!-- Affiche un message si aucun militaire n'est trouvé -->
                    <tr>
                        <td colspan="6" style="text-align:center;">Aucun militaire trouvé.</td>
                    </tr>
                <?php else: ?>
                    <!-- Boucle sur chaque militaire pour afficher sa ligne -->
                    <?php foreach ($militaires as $militaire): ?>
                        <tr>
                            <td>
                                <?php if (!empty($militaire['photo']) && file_exists($militaire['photo'])): ?>
                                    <!-- Affiche la photo du militaire si elle existe -->
                                    <img src="<?= htmlspecialchars($militaire['photo']) ?>" alt="Photo" style="width:50px;height:50px;border-radius:50%;">
                                <?php else: ?>
                                    <!-- Affiche un espace réservé si pas de photo -->
                                    <div style="width:50px;height:50px;background:#e0e0e0;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#aaa;">?</div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($militaire['nom']) ?></td>
                            <td><?= htmlspecialchars($militaire['grade']) ?></td>
                            <td><?= htmlspecialchars($militaire['unite'] ?? '-') ?></td>
                            <td><?= !empty($militaire['date_enrolement']) ? date('d/m/Y', strtotime($militaire['date_enrolement'])) : '-' ?></td>
                            <td>
                                <!-- Bouton modifier la fiche du militaire -->
                                <a href="modifier_militaire.php?id=<?= $militaire['id'] ?>" class="action-btn modif"><i class="fas fa-edit"></i></a>
                                <!-- Bouton imprimer la fiche individuelle du militaire -->
                                <a href="imprimer_militaire.php?id=<?= $militaire['id'] ?>" class="action-btn print"><i class="fas fa-print"></i></a>
                                <!-- Bouton supprimer le militaire avec confirmation -->
                                <a href="supprimer_mili.php?id=<?= $militaire['id'] ?>" class="action-btn suppr" onclick="return confirm('Supprimer ce militaire ?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination : liens page précédente et suivante -->
        <?php
        // Calcul du nombre total de pages
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
        <!-- Bouton pour imprimer la liste complète des militaires -->
        <a href="imprimer_liste.php" class="btn-imprimer" target="_blank">
            <i class="fas fa-print"></i> Imprimer la liste
        </a>
        <!-- Bouton flottant pour ajouter un militaire -->
        <a href="ajouter_militaire.php" class="btn-floating">+</a>
    </div>

    <!-- Pop-up message après ajout ou suppression d'un militaire -->
    <?php if (isset($_GET['msg'])): ?>
        <div id="popup-message" class="popup-message">
            <?php
                if ($_GET['msg'] === 'supprime') echo "Militaire supprimé avec succès !";
                elseif ($_GET['msg'] === 'ajoute') echo "Militaire ajouté avec succès !";
            ?>
        </div>
        <script>
            // Cache le pop-up après 2,5 secondes
            setTimeout(function() {
                document.getElementById('popup-message').style.display = 'none';
            }, 2500);
        </script>
    <?php endif; ?>
</body>
</html>