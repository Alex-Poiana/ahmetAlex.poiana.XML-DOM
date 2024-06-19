<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	$msg="";
	if (isset($_POST['invio'])){       // abbiamo appena inviato dati attraverso la form di login
	  if (empty($_POST['username']) || empty($_POST['password']))
		$msg="<p style=\"color: red; font-size: 1.3em\"> Dati mancanti!!! </p>\n";
	  else {  
			//variabile che indica se l'utente e' registrato. Se e' pari ad 0
			//significa che l'utente non e' registrato al sito, viceversa se e' pari a 1
			$utenteRegistrato=0; 
			
			$xmlString="";
			foreach (file("file_xml/tabellaUserCopia.xml") as $node){
				$xmlString.= trim($node);
			}
			
			$doc=new DOMDocument();
			$doc->loadXML($xmlString);
			
			$root=$doc->documentElement;
			$elementi=$root->childNodes;
			
			for ($i=0; $i<$elementi->length; $i++) {
				$elemento=$elementi->item($i);
				$username=$elemento->getElementsByTagName("username")->item(0)->textContent;
				$password=$elemento->getElementsByTagName("password")->item(0)->textContent;
				
				if (($_POST['username']==$username) && (($_POST['password']==$password))){
					$utenteRegistrato=1;
					$ban=$elemento->getElementsByTagName("ban")->item(0)->textContent;
					if ($ban!="attivo") {
						$userId=$elemento->getAttribute("userId");
						$tipologia=$elemento->getElementsByTagName("tipologia")->item(0)->textContent;
					
						session_start();
						$_SESSION['username']=$_POST['username'];
						$_SESSION['dataLogin']=time();
						$_SESSION['numeroUtente']=$userId;
						$_SESSION['accessoPermesso']=$tipologia;
						$_SESSION['ban']=$ban;
						if ($_SESSION['accessoPermesso']=="utente"){
							$sommeSpese=$elemento->getElementsByTagName("sommeSpese")->item(0)->textContent;
							$_SESSION['spesaFinora']=$sommeSpese;
						}
						header('Location: xml.OL.inizio.php');    // accesso alla pagina iniziale
						exit();			
					}
					else 
						$msg="<p style=\"color: red; font-size: 1.3em\"> Accesso negato!!! <br /> Causa: Ban. </p>\n";
				}
			}
			if ($utenteRegistrato==0)
				$msg= "<p style=\"color: red; font-size: 1.3em\"> Accesso negato!!! <br /> Causa: mancata registrazione. </p>\n";
		}
	}
?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title> Login </title>
		<link rel="stylesheet" href="stile.login-registrazione.css" type="text/css" />
	</head>

	<body>
		<div><a href="home.html"><img src="img/loghi/logoBalena.png" width="200" height="175" alt="Logo del sito: una balena"/></a></div>
		<hr />
		<div id="formContainer">
				<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
					<h2> Sign In </h2>
					
					<p>Username<br />
						<input type="text" name="username" size="30" />
					</p>
					
					<p>Password<br /> 
						<input type="password" name="password" size="30" />
					</p>
					
					<p id="signUp"><a href="xml.OL.registrazione.php"> Signup </a></p>
					
					<?php 
						echo $msg;
					?>
					
					<div id="buttons">
						<input type="submit" name="invio" value="Accedi" />
						<input type="reset" name="reset" value="Reset" />
					</div>
				</form>
			</div>
		<hr />
	</body>
</html>
