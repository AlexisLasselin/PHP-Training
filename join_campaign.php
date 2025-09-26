<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit();
}

$mysqli = require __DIR__ . '/database.php';

// Sécurité : Vérifier que campaign_id existe bien et est un entier
if (!isset($_POST['campaign_id']) || !ctype_digit($_POST['campaign_id'])) {
	die("Invalid campaign ID.");
}

$campaign_id = (int) $_POST['campaign_id'];
$player_id = $_SESSION['user_id'];

// Vérifier si le joueur est déjà inscrit à cette campagne
$sql = "SELECT id FROM registration WHERE player_id = ? AND campaign_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $player_id, $campaign_id);
$stmt->execute();
$stmt->store_result();

// Inscription
$sql = "INSERT INTO registration (player_id, campaign_id) VALUES (?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $player_id, $campaign_id);

if ($stmt->execute()) {
	header("Location: player_homepage.php");
	exit();
} else {
	echo "Erreur lors de l'inscription : " . $mysqli->error;
}
