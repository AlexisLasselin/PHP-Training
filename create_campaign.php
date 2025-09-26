<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit();
}

$mysqli = require __DIR__ . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'];
	$description = $_POST['description'];
	$date = $_POST['date'];

	$sql = "INSERT INTO campaigns (name, description, starting_date, dm_id) VALUES (?, ?, ?, ?)";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("sssi", $name, $description, $date, $_SESSION['user_id']);
	$stmt->execute();

	if ($stmt->affected_rows > 0) {
		header("Location: dm_homepage.php?message=Campaign created successfully");
		exit();
	} else {
		header("Location: dm_homepage.php?error=Failed to create campaign");
		exit();
	}
}
