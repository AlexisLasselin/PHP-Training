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
	<link rel="stylesheet" href="styles.css">
	<title>Login / Signup</title>
</head>

<body>
	<section class="forms-section">
		<h1 class="section-title">DnD Login / Signup</h1>
		<div class="forms">
			<div class="form-wrapper is-active">
				<button type="button" class="switcher switcher-login">
					Login
					<span class="underline"></span>
				</button>
				<form class="form form-login" method="post">
					<fieldset>
						<legend>Please, enter your email and password for login.</legend>
						<?php if (!empty($is_invalid)) : ?>
							<p style="color: red;">Invalid login</p>
						<?php endif; ?>
						<div class="input-block">
							<label for="email_login">E-mail</label>
							<input id="email_login" type="email" name="email" required>
						</div>
						<div class="input-block">
							<label for="password_login">Password</label>
							<input id="password_login" type="password" name="password" required>
						</div>
					</fieldset>
					<button type="submit" class="btn-login">Login</button>
				</form>
			</div>
			<div class="form-wrapper">
				<button type="button" class="switcher switcher-signup">
					Sign Up
					<span class="underline"></span>
				</button>
				<form class="form form-signup" action="register.php" method="post">
					<fieldset>
						<legend>Please, enter your email, password and password confirmation for sign up.</legend>
						<div class="input-block">
							<label for="username_signup">Username</label>
							<input type="text" id="username_signup" name="username" required>
						</div>
						<div class="input-block">
							<label for="email_signup">E-mail</label>
							<input id="email_signup" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" name="email" required>
						</div>
						<div class="input-block">
							<label for="password_signup">Password</label>
							<input id="password_signup" type="password" name="password" required>
						</div>
						<div class="input-block">
							<label for="confirm_password">Confirm password</label>
							<input id="confirm_password" type="password" name="confirm_password" required>
						</div>
						<div class="input-block">
							<label>Role</label>
							<div>
								<input type="radio" id="role_player" name="role" value="player" checked required>
								<label for="role_player">Player</label>

								<input type="radio" id="role_dm" name="role" value="dm" required>
								<label for="role_dm">DM</label>
							</div>
						</div>
					</fieldset>
					<button type="submit" class="btn-signup">Continue</button>
				</form>
			</div>
		</div>
	</section>


	<script>
		const switchers = [...document.querySelectorAll('.switcher')]

		switchers.forEach(item => {
			item.addEventListener('click', function() {
				switchers.forEach(item => item.parentElement.classList.remove('is-active'))
				this.parentElement.classList.add('is-active')
			})
		})
	</script>
</body>

</html>