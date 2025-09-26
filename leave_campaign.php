<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit();
}

$mysqli = require __DIR__ . '/database.php';

// Sécurité : vérifier que la valeur existe et est un entier
if (!isset($_POST['campaign_id']) || !ctype_digit($_POST['campaign_id'])) {
	die("Invalid campaign ID.");
}

$campaign_id = (int) $_POST['campaign_id'];
$player_id = $_SESSION['user_id'];

// Vérifier que le joueur est bien inscrit à cette campagne
$sql = "SELECT id FROM registration WHERE player_id = ? AND campaign_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $player_id, $campaign_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
	// Pas d'inscription trouvée
	header("Location: player_homepage.php?message=not_registered");
	exit();
}

// Supprimer l'inscription
$sql = "DELETE FROM registration WHERE player_id = ? AND campaign_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $player_id, $campaign_id);

if ($stmt->execute()) {
	header("Location: player_homepage.php?message=unregistered_success");
	exit();
} else {
	echo "Erreur lors de la désinscription : " . $mysqli->error;
}
