<?php
	session_start();
	
	if (!isset($_SESSION['accessoPermesso']) || $_SESSION['accessoPermesso']!="gestore")
		header ('Location: xml.OL.login.php');

	//variabile che quando vale 1 indica che si è verificato un errore
	//sull'inserimento del campo costo della form
	$erroreCosto=0;
	
	//variabile che quando vale 1 indica che si è verificato un errore
	//sull'inserimento del campo data della form
	$erroreData=0;
	
	if (isset($_POST['invio'])){
		if ($_POST['invio']=="Reset"){
			$_POST['nomeServizio']="";
			$_POST['data']="";
			$_POST['costo']="";
		}
		else {
			$msg=""; //contiene concatenazione di messaggi di errore
			
			//controllo che sia stato scelto il tipo di servizio
			if (!isset($_POST['nomeServizio'])){
				$msg="<div style=\"color: red; font-size: 0.8em\"> Errore: tipo servizio non specificato! </div>\n";
			}
			
			
			//controllo che sia stata inserita la data e che questa sia valida
			if (empty($_POST['data'])){
				$msg.="<div style=\"color: red; font-size: 0.8em\"> Errore: data non inserita! </div>\n";
				$erroreData=1;
			}
			else {
				//controllo che sia stato inserito il formato di data supportato (aaaa-mm-gg)
				if(preg_match('/^[0-9]{4}-{1}[0-9]{2}-{1}[0-9]{2}$/', $_POST['data'])){
					$data=explode("-",$_POST['data']); //ricavo l'anno, il mese e il giorno usando il "-" come separatore

					$anno = $data[0];	
					$mese = $data[1];
					$giorno = $data[2];
					
					//controllo che la data sia valida, cioè esista
					if (checkdate($mese, $giorno, $anno) != TRUE){ 
						$msg.="<div style=\"color: red; font-size: 0.8em\"> Errore: data non valida (inesistente)! </div>\n";
						$erroreData=1;
					}
				}
				else {
					$msg.="<div style=\"color: red; font-size: 0.8em\"> Errore: formato data non supportato! </div>\n";
					$erroreData=1;
				}	
			}
			
			
			//controllo che il campo costo non sia vuoto e sia un numero
			if (empty ($_POST['costo'])){
				$msg.="<div style=\"color: red; font-size: 0.8em\"> Errore: costo del servizio non inserito! </div>\n";
				$erroreCosto=1;
			}
			elseif (!is_numeric($_POST['costo'])){
				$msg.="<div style=\"color: red; font-size: 0.8em\"> Errore: il costo deve essere un numero! </div>\n";
				$erroreCosto=1;
			}
			
			
			//se i controlli precedenti sono andati, si procede con l'inserimento del servizio nel db
			if (empty($msg)){
				$xmlString="";
				switch ($_POST['nomeServizio']) {
					case "Whale Watching":
						foreach (file("file_xml/tabellaWhaleWatchCopia.xml") as $node)
							$xmlString.=trim($node);
						
						$doc=new DOMDocument();
						if (!$doc->loadXML($xmlString))
							die ("Errore nel parsing del documenti tabellaWhaleWatchCopia.xml");
						
						$root=$doc->documentElement;
						$elementi=$root->childNodes;
						$numElementi=$elementi->length;
						$numElementi++;
						$newService=$doc->createElement("service");
						$newService->setAttribute("serviceId", "ww$numElementi");
						$newNomeServizio=$doc->createElement("nomeServizio", "{$_POST['nomeServizio']}");
						$newData=$doc->createElement("data", "{$_POST['data']}");
						$newCosto=$doc->createElement("costo", "{$_POST['costo']}");
						$newService->appendChild($newNomeServizio);
						$newService->appendChild($newData);
						$newService->appendChild($newCosto);
						$root->appendChild($newService);
						
						$path=dirname(__FILE__). "/file_xml/tabellaWhaleWatchCopia.xml";
						$doc->save($path);
					break;
					
					case "Dolphin Swimming":
						foreach (file("file_xml/tabellaDolphinSwimCopia.xml") as $node)
							$xmlString.=trim($node);
						
						$doc=new DOMDocument();
						if (!$doc->loadXML($xmlString))
							die ("Errore nel parsing del documenti tabellaDolphinSwimCopia.xml");
						
						$root=$doc->documentElement;
						$elementi=$root->childNodes;
						$numElementi=$elementi->length;
						$numElementi++;
						$newService=$doc->createElement("service");
						$newService->setAttribute("serviceId", "ds$numElementi");
						$newNomeServizio=$doc->createElement("nomeServizio", "{$_POST['nomeServizio']}");
						$newData=$doc->createElement("data", "{$_POST['data']}");
						$newCosto=$doc->createElement("costo", "{$_POST['costo']}");
						$newService->appendChild($newNomeServizio);
						$newService->appendChild($newData);
						$newService->appendChild($newCosto);
						$root->appendChild($newService);
						
						$path=dirname(__FILE__). "/file_xml/tabellaDolphinSwimCopia.xml";
						$doc->save($path);
					break;
					
					case "Shark Diving":
						foreach (file("file_xml/tabellaSharkDiveCopia.xml") as $node)
							$xmlString.=trim($node);
						
						$doc=new DOMDocument();
						if (!$doc->loadXML($xmlString))
							die ("Errore nel parsing del documenti tabellaSharkDiveCopia.xml");
						
						$root=$doc->documentElement;
						$elementi=$root->childNodes;
						$numElementi=$elementi->length;
						$numElementi++;
						$newService=$doc->createElement("service");
						$newService->setAttribute("serviceId", "sh$numElementi");
						$newNomeServizio=$doc->createElement("nomeServizio", "{$_POST['nomeServizio']}");
						$newData=$doc->createElement("data", "{$_POST['data']}");
						$newCosto=$doc->createElement("costo", "{$_POST['costo']}");
						$newService->appendChild($newNomeServizio);
						$newService->appendChild($newData);
						$newService->appendChild($newCosto);
						$root->appendChild($newService);
						
						$path=dirname(__FILE__). "/file_xml/tabellaSharkDiveCopia.xml";
						$doc->save($path);
					break;	
				}
				
				$msg="<p style=\"color: green; font-size: 0.8em\"> Operazione eseguita con successo: servizio {$_POST['nomeServizio']} aggiunto correttamente! </p>\n";
				
				//aggiunto il servizio, svuoto i campi della form
				$_POST['nomeServizio']="";
				$_POST['data']="";
				$_POST['costo']="";
			}
		}	
	}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title> Aggiunta Servizi </title>
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
				<h2> Aggiunta nuovi servizi </h2>
				<p> In questa sezione puoi aggiungere nuovi servizi compilando la form che segue. </p>
					
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<p> Specifica il tipo di servizio da inserire: <br />
						<input type="radio" name="nomeServizio" value="Whale Watching" 
							<?php if (isset($_POST['nomeServizio']) && $_POST['nomeServizio']=="Whale Watching") echo "checked=\"checked\"";?> /> Whale Watching <br />
							
						<input type="radio" name="nomeServizio" value="Dolphin Swimming" 
							<?php if (isset($_POST['nomeServizio']) && $_POST['nomeServizio']=="Dolphin Swimming") echo "checked=\"checked\"";?> /> Dolphin Swimming <br />
							
						<input type="radio" name="nomeServizio" value="Shark Diving" 
								<?php if (isset($_POST['nomeServizio']) && $_POST['nomeServizio']=="Shark Diving") echo "checked=\"checked\"";?> /> Shark Diving
					</p>
					
					<div>
						<span> Data (aaaa-mm-dd) <input type="text" name="data" value="<?php if (isset($_POST['data']) && $erroreData!=1) echo $_POST['data'];?>" size="20" /> </span>
						<span style="margin-left: 3%"> Costo <input type="text" name="costo" value="<?php if (isset($_POST['costo']) && $erroreCosto!=1) echo $_POST['costo'];?>" size="20" /></span>
					</div>
					
					<p>
						<input type="submit" name="invio" value="Aggiungi servizio" />
						<input type="submit" name="invio" value="Reset" />
					</p>
				</form>
				<hr />
				<?php if (isset ($msg)) echo $msg. "<hr />";?>
				
				<!-- per controllare il contenuto di $_POST -->
				<p> $_POST: <?php print_r ($_POST); ?> </p>	
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