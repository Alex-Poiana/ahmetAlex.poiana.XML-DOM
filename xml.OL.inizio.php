<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	session_start();

	/*questo e' il controllo di accesso: se non e' stata prima effettuata 
		l'autenticazione, la variabile SESSION accessoPermesso non esiste ... */
	if (!isset($_SESSION['accessoPermesso'])) 
	  header('Location: xml.OL.login.php');
  
	//Messaggio di inizio diverso a seconda della tipologia di utente
	$msg="";
	switch ($_SESSION['accessoPermesso']){
		
			case "utente": 
				$msg="<p> Caro {$_SESSION['accessoPermesso']}, <br />\n\t\t\t\t\t ora hai la possibilit&agrave; di acquistare ed usufruire dei nostri unici servizi. 
					Fidati, non te ne pentirai! Tramite il men&ugrave; puoi effettuare una serie di operazioni.\n\t\t\t\t</p>\n";
			break;
		
			case "admin": 
				$msg="<p> Caro {$_SESSION['accessoPermesso']}, <br />\n\t\t\t\t\tquale sventurato utente del sito subir&agrave; la tua ira?? 
					Beh, solo tu puoi saperlo.. Ma non prenderci troppo la mano, mi raccomando.\n\t\t\t\t</p>\n";
			break;
		
			case "gestore": 
				$msg="<p> Caro {$_SESSION['accessoPermesso']}, <br />\n\t\t\t\t\tpuoi eseguire una serie di operazioni di gestione come quelle 
					mostrate nel men&ugrave;.\n\t\t\t\t</p>\n";
			break;
		}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title> Main Page </title>
		<link rel="stylesheet" href="stile.pag.iniziali.css" type="text/css" />
	</head>

	<body id="container">
		<div id="header">
			<div id="firstRowHeader">
				<div><a href="xml.OL.logout.php"><img src="img/loghi/logoBalena.png" width="200" height="175" alt="Logo del sito: una balena"/></a></div>
				<div id="login"><a href="xml.OL.logout.php" >Logout</a></div>
			</div>
			
			<div id="secondRowHeader">
				<span class="social"><a href=""><img src="img/loghi/logoFacebook.png" width="50" height="50" alt="Logo facebook"/></a></span>
				<span class="social"><a href=""><img src="img/loghi/logoInsta.png" width="50" height="50" alt="Logo instagram"/></a></span>
				<span class="social"><a href=""><img src="img/loghi/logoYoutube.png" width="50" height="50" alt="Logo youtube"/></a></span>
				<span class="social"><a href=""><img src="img/loghi/logoTrip.png" width="50" height="50" alt="Logo tripadvisor"/></a></span>
			</div>
		</div>
			
		<div id="content">
			<div id="centralBox">
				<h2> Benvenuto <?php echo $_SESSION['username'];?> !!! </h2>
				<p> Ti sei collegato alle <?php echo date('g:i a', $_SESSION['dataLogin']);?> </p>
				<?php echo $msg; ?>
				
				<hr />
				<?php
					//stampa di array superglobals per controllo
					echo "<p> ";
					echo "\$_COOKIE: ";
					print_r ($_COOKIE);
					echo "<br />\n";
					echo "\$_POST: ";
					print_r ($_POST);
					echo "<br />\n";		
					echo "\$_SESSION: ";
					print_r ($_SESSION);
					echo " </p>";
				?>	
			</div>
			
			<div id="leftBox">
				<?php require("menu.php"); ?>
			</div>
		</div>
		
		<div id="footer">
			<div id="firstRowFooter"> 
				<div>
					<img src="img/loghi/logoBalena.png" width="100" height="75" alt="Logo del sito: una balena"/>
					<ul>
						<li id="posizione"> Al Fanar, Sharm Al Sheikh, <br />South Sinai, Egitto </li>
						<li id="telefono">+39 342 598 6245</li>
						<li id="mail">info@oceanlovers.com</li>	
					</ul>
				</div>
				
				<div>
					<h2> Centro Sub </h2><br />
					<p>Immersioni</p>
					<p>Corsi</p>
					<p>Snorkeling</p>
				</div>
				
				<div>
					<h2> Crociere </h2><br />
					<p> Crociere sub </p>
					<p> Crociere barche a vela </p>
				</div>
				
				<div>
					<h2> Desclaimer </h2><br />
					<p> Job Opportunities </p>
					<p> Privacy Policy </p>
					<p> Cookie Policy </p>
					<p> Termini e condizioni </p>
				</div>
			</div>
			
			<div id="secondRowFooter">Copyright &copy; 2024 Ocean Lovers a Sharm El Sheikh - 
				All Rights Reserved - Sviluppo by Realizzazione siti L-web Roma
			</div>
		</div>
	</body>
</html>



