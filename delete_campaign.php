<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
	echo json_encode(["success" => false, "error" => "Not logged in"]);
	exit();
}

if (!isset($_GET['id'])) {
	echo json_encode(["success" => false, "error" => "No campaign ID provided"]);
	exit();
}

$campaign_id = intval($_GET['id']);
$mysqli = require __DIR__ . '/database.php';

$sql = "SELECT campaign_id FROM campaigns WHERE campaign_id = ? AND dm_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $campaign_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
	echo json_encode(["success" => false, "error" => "Unauthorized"]);
	exit();
}

$sql = "DELETE FROM campaigns WHERE campaign_id = ? AND dm_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $campaign_id, $_SESSION['user_id']);

if ($stmt->execute()) {
	echo json_encode(["success" => true, "id" => $campaign_id]);
} else {
	echo json_encode(["success" => false, "error" => "Database error"]);
}
