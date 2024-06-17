<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	//variabile che quando vale 1 indica che non e' possibile usare lo username inserito
	// poiche' gia' usato da un altro utente del sito
	$errore=0;
	
	if (isset($_POST['invio'])){
		if ($_POST['invio']=="Reset"){
			$_POST['username']="";
			$_POST['genere']="";
			$_POST['nazione']="";
		}
		else {
			$msg=""; //contiene concatenazione di messaggi di errore
			
			$xmlString="";
			foreach (file("file_xml/tabellaUserCopia.xml") as $node)
					$xmlString.=trim($node);
					
			$doc=new DOMDocument();
			if (!$doc->loadXML($xmlString))
				die ("Errore nel parsing del documento tabellaUserCopia.xml");
					
			$root=$doc->documentElement;
			$elementi=$root->childNodes;
			
			
			//controllo che sia stato inserito lo username
			if (empty($_POST['username'])){
				$msg="<div style=\"color: red; font-size: 1.1em\"> Errore: username non inserito! </div>\n";
			}
			else {
				//controllo che lo username inserito sia univoco 
				//(non ci siano due utenti con lo stesso username)
				
				for ($i=0; $i<$elementi->length; $i++) {
					$elemento=$elementi->item($i);
					$username=$elemento->getElementsByTagName("username")->item(0)->textContent;
					if ($_POST['username']==$username)
						$errore=1;
				}
				
				if ($errore==1)
					$msg="<div style=\"color: red; font-size: 1.1em\"> Errore: username non disponibile! </div>\n";
			}
			
			
			
			//controllo che sia stata inserita la password
			if (empty($_POST['password'])){
				$msg.="<div style=\"color: red; font-size: 1.1em\"> Errore: password non inserita! </div>\n";
			}
			else {
				//controllo che le password coincidano
				if ($_POST['password']!=$_POST['confPassword'])
					$msg.="<div style=\"color: red; font-size: 1.1em\"> Errore: le password non coincidono! </div>\n";
				}
			
			
			//controllo che sia stato scelto il genere
			if (!isset ($_POST['genere'])){
				$msg.="<div style=\"color: red; font-size: 1.1em\"> Errore: genere non specificato! </div>\n";
			}
			
			//controllo che sia stato scelta la nazione
			if ( $_POST['nazione']=="Scegli Nazione"){
				$msg.="<div style=\"color: red; font-size: 1.1em\"> Errore: nazione non specificata! </div>\n";
			}
			
			
			//se i controlli precedenti sono andati, si procede con l'inserimento del prodotto nel db
			if (empty($msg)){
				
				//ricavo il numero di user presenti nel file tabellaUserCopia.xml necessario
				//per l'assegnazione dello userId all'utente che si sta registrando
				$numElementi=$elementi->length;
				$numElementi++;
				
				$newUser=$doc->createElement("user");
				$newUser->setAttribute("userId", "user$numElementi");
				$newUsername=$doc->createElement("username", "{$_POST['username']}");
				$newPassword=$doc->createElement("password", "{$_POST['password']}");
				$newGenere=$doc->createElement("genere", "{$_POST['genere']}");
				$newNazione=$doc->createElement("nazione", "{$_POST['nazione']}");
				$newTipologia=$doc->createElement("tipologia", "utente");
				$newSommeSpese=$doc->createElement("sommeSpese", "0");
				$newBan=$doc->createElement("ban", "nonAttivo");
				$newUser->appendChild($newUsername);
				$newUser->appendChild($newPassword);
				$newUser->appendChild($newGenere);
				$newUser->appendChild($newNazione);
				$newUser->appendChild($newTipologia);
				$newUser->appendChild($newSommeSpese);
				$newUser->appendChild($newBan);
				$root->appendChild($newUser);
				
				$path=dirname(__FILE__). "/file_xml/tabellaUserCopia.xml";
				$doc->save($path);
				
				$msg="<p style=\"color: green; font-size: 1.1em\"> Registrazione Completata! <br />Torna al login per accedere. </p>\n";
				
				$_POST['username']="";
				$_POST['genere']="";
				$_POST['nazione']="";
			}
		}	
	}
?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title> Registrazione </title>
		<link rel="stylesheet" href="stile.login-registrazione.css" type="text/css" />
	</head>

	<body>
		<div><a href="home.html"><img src="img/loghi/logoBalena.png" width="200" height="175" alt="Logo del sito: una balena"/></a></div>
		<hr />
		<div id="formContainer">
				<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
					<h2> Sign Up </h2>
					
					<p> Username <br />
						<input type="text" name="username" value="<?php if (isset($_POST['username'])&& $errore!=1) echo $_POST['username'];?>" size="30" />
					</p>
					
					<p> Password <br /> 
						<input type="text" name="password" size="30" />
					</p>
					
					<p> Conferma Password <br /> 
						<input type="text" name="confPassword" size="30" />
					</p>
					
					<p> Genere: <br /> 
						<input type="radio" name="genere" value="femmina" 
							<?php if (isset($_POST['genere']) && $_POST['genere']=="femmina") echo "checked=\"checked\"";?> /> Femmina
						<input type="radio" name="genere" value="maschio" 
							<?php if (isset($_POST['genere']) && $_POST['genere']=="maschio") echo "checked=\"checked\"";?>/> Maschio	
					</p>
					
					<p> Nazione <br />
						<select  name="nazione" size="1">
							<option value="Scegli Nazione">Scegli Nazione</option>
							<option value="Belgio" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Belgio") echo "selected=\"selected\"";?>>Belgio</option>
							<option value="Italia" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Italia") echo "selected=\"selected\"";?>>Italia</option>
							<option value="Francia" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Francia") echo "selected=\"selected\"";?>>Francia</option>
							<option value="Spagna" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Spagna") echo "selected=\"selected\"";?>>Spagna</option>
							<option value="Germania" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Germania") echo "selected=\"selected\"";?>>Germania</option>
							<option value="Regno Unito" <?php if (isset($_POST['nazione']) && $_POST['nazione']=="Regno Unito") echo "selected=\"selected\"";?>>Regno Unito</option>
						</select>
					</p>
					
					<p id="signUp"><a href="xml.OL.login.php"> Signin </a></p>
					<?php if (isset($msg))echo $msg; ?>
					
					<div id="buttons">
						<input type="submit" name="invio" value="Registrati" />
						<input type="submit" name="invio" value="Reset" />
					</div>
				</form>
			</div>
		<hr />
		<!-- stampa di $_POST per controllo 
		<p> $_POST: <?php print_r($_POST); ?> </p> -->
	</body>
</html>
