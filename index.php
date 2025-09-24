<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$is_invalid = false;

	$mysqli = require __DIR__ . '/database.php';

	$sql = sprintf(
		"SELECT * FROM user WHERE email = '%s'",
		$mysqli->real_escape_string($_POST['email'])
	);

	$result = $mysqli->query($sql);

	$user = $result->fetch_assoc();

	if ($user) {
		if (password_verify($_POST['password'], $user['password_hash'])) {
			session_start();

			session_regenerate_id();

			$_SESSION['user_id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['role'] = $user['role'];

			if ($user['role'] === 'player') {
				header('Location: player_homepage.php');
				exit;
			} elseif ($user['role'] === 'dm') {
				header('Location: dm_homepage.php');
				exit;
			}
		}
	}

	$is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login / Signup</title>
</head>

<body>
	<div class="container">
		<!-- LOGIN FORM -->
		<div id="loginForm">
			<h1>Login</h1>
			<?php if (!empty($is_invalid)) : ?>
				<p style="color: red;">Invalid login</p>
			<?php endif; ?>
			<form method="post">
				<div class="form-group">
					<label for="email_login">Email:</label>
					<input type="email" id="email_login" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
				</div>
				<div class="form-group">
					<label for="password_login">Password:</label>
					<input type="password" id="password_login" name="password" required>
				</div>
				<button type="submit">Login</button>
			</form>
			<div class="switch-link">
				<p>Don't have an account? <a href="#" onclick="showSignup()">Register here</a></p>
			</div>
		</div>

		<!-- SIGNUP FORM -->
		<div id="signupForm" style="display:none;">
			<h1>Sign Up</h1>
			<form action="register.php" method="post">
				<div class="form-group">
					<label for="username_signup">Username:</label>
					<input type="text" id="username_signup" name="username" required>
				</div>
				<div class="form-group">
					<label for="email_signup">Email:</label>
					<input type="email" id="email_signup" name="email" required>
				</div>
				<div class="form-group">
					<label for="password_signup">Password:</label>
					<input type="password" id="password_signup" name="password" required>
				</div>
				<div class="form-group">
					<label for="confirm_password">Confirm Password:</label>
					<input type="password" id="confirm_password" name="confirm_password" required>
				</div>
				<div class="form-group">
					<label>Role:</label><br>
					<input type="radio" id="role_player" name="role" value="player" checked required>
					<label for="role_player">Player</label>

					<input type="radio" id="role_dm" name="role" value="dm" required>
					<label for="role_dm">DM</label>
				</div>
				<button type="submit">Register</button>
			</form>
			<div class="switch-link">
				<p>Already have an account? <a href="#" onclick="showLogin()">Login here</a></p>
			</div>
		</div>
	</div>

	<script>
		function showSignup() {
			document.getElementById('loginForm').style.display = 'none';
			document.getElementById('signupForm').style.display = 'block';
		}

		function showLogin() {
			document.getElementById('signupForm').style.display = 'none';
			document.getElementById('loginForm').style.display = 'block';
		}
	</script>
</body>

</html>