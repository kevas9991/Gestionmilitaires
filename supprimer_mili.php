<?php
// Démarre la session et vérifie l'authentification admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php'); // Redirige si non admin
    exit();
}

require_once 'db.php';
require_once 'auth.php';

// Vérifie si l'ID du militaire à supprimer est présent dans l'URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sécurise l'ID
    // Prépare et exécute la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM militaires WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirige vers la liste avec un message de suppression
header("Location: gestion_personnel.php?msg=supprime");
exit();
?>
