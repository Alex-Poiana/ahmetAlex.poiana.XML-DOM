<?php
	session_start();
	
	if (!isset($_SESSION['accessoPermesso']) || $_SESSION['accessoPermesso']!="gestore")
		header ('Location: xml.OL.login.php');
	
	$xmlString="";
	foreach (file("file_xml/tabellaUserCopia.xml") as $node){
			$xmlString.= trim($node);
	}
			
	$doc=new DOMDocument();
	if (!$doc->loadXML($xmlString))
		die ("Errore nel parsing del documento tabellaUserCopia.xml!\n");
			
	$root=$doc->documentElement;
	$elementi=$root->childNodes;
	
	//construzione tabella di selezione utenti
	$tableUsers="<table border=\"2px\" cellspacing=\"2px\" cellpadding=\"3px\" style=\"border-color: fuchsia; font-size: 70% \">\n";
	$tableUsers.="<thead>\n <tr>\n <th></th>\n <th> Username </th>\n <th> Genere </th>\n <th> Nazione </th>\n";
	$tableUsers.=" <th> Tipologia Utente </th>\n <th> Somme Spese </th>\n <th> Ban </th>\n </tr>\n</thead>\n\n<tbody>\n";
	
	for ($i=0; $i<$elementi->length; $i++) {
		$elemento=$elementi->item($i);
		$tipologia=$elemento->getElementsByTagName("tipologia")->item(0)->textContent;
		if ($tipologia=="utente"){
			$username=$elemento->getElementsByTagName("username")->item(0)->textContent;
			$ban=$elemento->getElementsByTagName("ban")->item(0)->textContent;
			$sommeSpese=$elemento->getElementsByTagName("sommeSpese")->item(0)->textContent;
			$genere=$elemento->getElementsByTagName("genere")->item(0)->textContent;
			$nazione=$elemento->getElementsByTagName("nazione")->item(0)->textContent;
	
			$tableUsers.="<tr>\n <td> <input type=\"radio\" name=\"selection\" value=\"$username\" /> </td>\n";
			$tableUsers.=" <td> $username </td>\n <td> $genere </td>\n <td> $nazione </td>\n";
			$tableUsers.=" <td> $tipologia </td>\n <td> $sommeSpese </td>\n <td> $ban </td>\n</tr>\n\n";
		}
	}

	$tableUsers.="</tbody>\n </table>\n";
	

	if (isset ($_POST['invio'])) {
		if (isset($_POST['selection'])) {
			for ($i=0; $i<$elementi->length; $i++) {
				$elemento=$elementi->item($i);
				$username=$elemento->firstChild->textContent;
				if ($username==$_POST['selection']){
					$sommeSpese=$elemento->getElementsByTagName("sommeSpese")->item(0)->textContent;
					if ($sommeSpese!=0){
						
						//tabella che conterra' gli acquisti dell'utente selezionato
						$tableService="\n<table border=\"2px\" cellspacing=\"2px\" cellpadding=\"3px\" style=\"border-color: fuchsia; font-size: 70% \">\n";
						$tableService.="<thead>\n <tr>\n <th> ServiceId </th>\n <th> Nome Servizio </th>\n <th> Data </th>\n <th> Costo </th>\n";
						$tableService.=" <th> Quantit&agrave; </th>\n </tr>\n</thead>\n\n<tbody>\n";
						
						$serviziAcquistati=$elemento->lastChild->childNodes;
						$numServiziAcquistati=$serviziAcquistati->length;
						for ($j=0; $j<$numServiziAcquistati; $j++){
							$servizio=$serviziAcquistati->item($j);
							$serviceIdUs=$servizio->getAttribute("serviceId");
							$tipoServizio=$servizio->getAttribute("tipoServizio");
							$quanteCopie=$servizio->getAttribute("quanteCopie");
							
							$xmlStr="";
							
							switch ($tipoServizio){
								case "whaleWatching":
									foreach (file("file_xml/tabellaWhaleWatchCopia.xml") as $node)
									$xmlStr.=trim($node);
										
									$doc2=new DOMDocument();
									if (!$doc2->loadXML($xmlStr))
										die ("Errore nel parsing del documento tabellaWhaleWatchCopia.xml");
						
									$root2=$doc2->documentElement;
									$elementi2=$root2->childNodes;
									for ($k=0; $k<$elementi2->length; $k++){
										$elemento2=$elementi2->item($k);
										$serviceId=$elemento2->getAttribute("serviceId");
										if ($serviceIdUs==$serviceId) {
											$nomeServizio=$elemento2->firstChild->textContent;
											$data=$elemento2->getElementsByTagName("data")->item(0)->textContent;
											$costo=$elemento2->lastChild->textContent;
										}
									}
							
								break;
							
								case "dolphinSwimming":
									foreach (file("file_xml/tabellaDolphinSwimCopia.xml") as $node)
									$xmlStr.=trim($node);
										
									$doc2=new DOMDocument();
									if (!$doc2->loadXML($xmlStr))
										die ("Errore nel parsing del documento tabellaDolphinSwimCopia.xml");
						
									$root2=$doc2->documentElement;
									$elementi2=$root2->childNodes;
									for ($k=0; $k<$elementi2->length; $k++){
										$elemento2=$elementi2->item($k);
										$serviceId=$elemento2->getAttribute("serviceId");
										if ($serviceIdUs==$serviceId) {
											$nomeServizio=$elemento2->firstChild->textContent;
											$data=$elemento2->getElementsByTagName("data")->item(0)->textContent;
											$costo=$elemento2->lastChild->textContent;
										}
									}
							
								break;
								
								case "sharkDiving":
									foreach (file("file_xml/tabellaSharkDiveCopia.xml") as $node)
									$xmlStr.=trim($node);
										
									$doc2=new DOMDocument();
									if (!$doc2->loadXML($xmlStr))
										die ("Errore nel parsing del documento tabellaSharkDiveCopia.xml");
						
									$root2=$doc2->documentElement;
									$elementi2=$root2->childNodes;
									for ($k=0; $k<$elementi2->length; $k++){
										$elemento2=$elementi2->item($k);
										$serviceId=$elemento2->getAttribute("serviceId");
										if ($serviceIdUs==$serviceId) {
											$nomeServizio=$elemento2->firstChild->textContent;
											$data=$elemento2->getElementsByTagName("data")->item(0)->textContent;
											$costo=$elemento2->lastChild->textContent;
										}
									}
							
								break;
							}
							
							$tableService.=" <tr>\n <td> $serviceIdUs </td>\n <td> $nomeServizio </td>\n";
							$tableService.=" <td> $data </td>\n <td> $costo </td>\n <td> $quanteCopie </td>\n </tr>\n\n";
						}
						
						$tableService.="</tbody>\n</table>\n";	
					}
					else 
						$msg="<div style=\"color: green; font-size: 0.8em\"> L'utente {$_POST['selection']} non ha effettuato alcun acquisto. </div>";
				}
			}
		}
		else 
			$msg="<div style=\"color: red; font-size: 0.8em\"> Operazione fallita, utente non selezionato. </div>";
	}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title> Elenco Acquisti </title>
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
				<h2> Elenco acquisti utente </h2>
				<p> In questa sezione puoi visualizzare gli acquisti effettuati degli utenti del sito. </p>
				<p> Elenco utenti del sito: </p>
				<form action="<?php $_SERVER['PHP_SELF']?>"  method="post" >
					<?php echo $tableUsers; ?>
					<p>
						<input type="submit" name="invio" value="Vedi Acquisti"/>
						<input type="reset" value="Annulla selezionato" />
					</p>
				</form>
				<hr />
				<?php 
					if (isset ($msg)) echo $msg. "<hr />";
					
					if (isset ($tableService) ) {
						echo "<h4> Ecco i servizi acquistati dall'utente {$_POST['selection']}: </h4>";
						echo $tableService. "<hr />";
					}
				?>
				
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