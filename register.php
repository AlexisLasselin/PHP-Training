<?php

filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) or die("Invalid email format.");

if (strlen($_POST['password']) < 8) {
	die("Password must be at least 8 characters long.");
}

if (!preg_match("/[A-Z]/", $_POST['password'])) {
	die("Password must contain at least one uppercase letter.");
}

if (!preg_match("/[a-z]/", $_POST['password'])) {
	die("Password must contain at least one lowercase letter.");
}

if (!preg_match("/[0-9]/", $_POST['password'])) {
	die("Password must contain at least one number.");
}

if (!preg_match("/[\W]/", $_POST['password'])) {
	die("Password must contain at least one special character.");
}

if ($_POST['password'] !== $_POST['confirm_password']) {
	die("Passwords do not match.");
}

$username = $_POST['username'];
$email = $_POST['email'];
$passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$conn = require __DIR__ . '/database.php';

$sql = "INSERT INTO user (username, email, password_hash, role) VALUES (?, ?, ?, ?)";

if (! $stmt = $conn->prepare($sql)) {
	die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssss", $username, $email, $passwordHash, $role);

if ($stmt->execute()) {
	if ($role === 'player') {
		header("Location: player_homepage.php");
	} elseif ($role === 'dm') {
		header("Location: dm_homepage.php");
	}
} else {
	if ($conn->errno === 1062) {
		die("Error: Email already registered.");
	} else {
		die("Execute failed: " . $stmt->error);
	}
}

$stmt->close();
