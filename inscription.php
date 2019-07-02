<?php
	session_start();

	require('src/log.php');

	if(isset($_SESSION['connect'])){
		header('location: index.php');
		exit();
	}
	
	if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two']) ){
		
		require('src/connect.php');

		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		$passwordTwo = htmlspecialchars($_POST['password_two']);

		//PASSWORD == PASSWORDTWO
		if($password != $passwordTwo){
			header('location: inscription.php?error=1&message=Vos mots de passe ne correspondent pas');
			exit();
		}

		//VALIDATE MAIL
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: inscription.php?error=1&message=Votre adresse mail est invalide.');
			exit();
		}



		//EMAIL ALREADY EXIST
		$req= $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));

		while($email_verification = $req->fetch()){
			
			if($email_verification['numberEmail'] != 0){
				header('location: inscription.php?error=1&message=Votre adresse mail est déjà utilisée');
				exit();	
			}

		}

		//HASH
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		//CHIFFRAGE PASSWORD
		$password = "2564".sha1($password."aq21")."24";

		//SENDING
		$req = $db->prepare("INSERT INTO user(email, password, secret) VALUE(?,?,?)");
		$req->execute(array($email, $password, $secret));

		header('location: inscription.php?success=1');
		exit();

	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php 
			
			if(isset($_GET['error'])){
				
				if(isset($_GET['message'])){
					echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
				}

			}elseif(isset($_GET['success'])){
				echo '<div class="alert success">Vous êtes désormais inscrit(e).<br/><a href="index.php">Connectez-vous</a></div>';
			} 
			?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>