<?php
	error_reporting (E_ALL &~E_NOTICE);

	session_start();               

	if (!isset($_SESSION['accessoPermesso']) || $_SESSION['accessoPermesso']!="utente")
	header('Location: xml.OL.login.php');

	// variabile contenente la stringa di output che verra` inclusa nella pagina di risposta
	$msg="";
	
	if ($_SESSION['costoCarrello']!=0) {
		$msg.="<p> Gentile cliente {$_SESSION['username']}";
	
		$tot=$_SESSION['costoCarrello']+$_SESSION['spesaFinora'];
		
		$trovato=0;	
		$xmlString="";
		foreach (file("file_xml/tabellaUserCopia.xml") as $node){
			$xmlString.= trim($node);
		}
			
		$doc=new DOMDocument();
		if (!$doc->loadXML($xmlString))
			die ("Errore nel parsing del documento!\n");
			
		$root=$doc->documentElement;
		$elementi=$root->childNodes;
			
		//ricerca dello user che ha effettuato gli acquisti nel file tabellaUserCopia.xml 
		for ($i=0; $i<$elementi->length; $i++) {
			$elemento=$elementi->item($i);
			$username=$elemento->firstChild->textContent;
			if ($username==$_SESSION['username']){
					
				//aggiornamento elemento <sommeSpese> per l'utente che ha effettuato
				//gli acquisti.
				$elemSommeSpese=$elemento->getElementsByTagName("sommeSpese")->item(0);
				$newSommeSpese=$doc->createElement("sommeSpese", "$tot");
				$elemento->replaceChild($newSommeSpese, $elemSommeSpese);
					
				//controllo esistenza elem <prodottiAcquistati>
				if ($_SESSION['spesaFinora']==0) {
					//creazione di <serviziAcquistati>
					$elemServiziAcquistati=$doc->createElement("serviziAcquistati");
					$elemento->appendChild($elemServiziAcquistati);
				}
				else {
					//si ottiene <serviziAcquistati> in quanto gia' esistente
					//(evidentemente l'utente ha gia' effettuato acquisti)
					$elemServiziAcquistati=$elemento->getElementsByTagName("serviziAcquistati")->item(0);
				}
					
				//inserimento del contenuto del carrello, nell'elem <serviziAcquistati>
				foreach ($_SESSION['carrello'] as $chiave=>$valore) {
					//ricerca dell'elemento nel carrello in tabellaWhaleWatchCopia.xml
					//per capire di che tipo di servizio si tratta
					$xmlString2="";
					foreach (file("file_xml/tabellaWhaleWatchCopia.xml") as $node)
						$xmlString2.=trim($node);
							
					$doc2=new DOMDocument();
					if (!$doc2->loadXML($xmlString2))
						die("Errore nel parsing del documento tabellaWhaleWatchCopia.xml\n");
						
					$elementi2=$doc2->documentElement->childNodes;
						
					for ($j=0; $j<$elementi2->length; $j++){
						$elemento2=$elementi2->item($j);
						$serviceId=$elemento2->getAttribute("serviceId");
						if ($serviceId==$valore){
							//e' un servizio whaleWatch e ne conservo il tipoServizio
							$trovato=1;
							$tipoServizio="whaleWatching";
						}
					}
					
					//ricerca dell'elemento nel carrello in tabellaDolphinSwimCopia.xml
					//per capire di che tipo di servizio si tratta
					if ($trovato==0) {
						$xmlString2="";
						foreach (file("file_xml/tabellaDolphinSwimCopia.xml") as $node)
							$xmlString2.=trim($node);
								
						$doc2=new DOMDocument();
						if (!$doc2->loadXML($xmlString2))
							die("Errore nel parsing del documento tabellaDolphinSwimCopia.xml\n");
							
						$elementi2=$doc2->documentElement->childNodes;
							
						for ($j=0; $j<$elementi2->length; $j++){
							$elemento2=$elementi2->item($j);
							$serviceId=$elemento2->getAttribute("serviceId");
							if ($serviceId==$valore){
								$trovato=1;
								$tipoServizio="dolphinSwimming";
							}
						}
					}
					
					//ricerca dell'elemento nel carrello in tabellaSharkDiveCopia.xml
					//per capire di che tipo di servizio si tratta
					if ($trovato==0) {
						$xmlString2="";
						foreach (file("file_xml/tabellaSharkDiveCopia.xml") as $node)
							$xmlString2.=trim($node);
								
						$doc2=new DOMDocument();
						if (!$doc2->loadXML($xmlString2))
							die("Errore nel parsing del documento tabellaSharkDiveCopia.xml\n");
							
						$elementi2=$doc2->documentElement->childNodes;
							
						for ($j=0; $j<$elementi2->length; $j++){
							$elemento2=$elementi2->item($j);
							$serviceId=$elemento2->getAttribute("serviceId");
							if ($serviceId==$valore){
								$tipoServizio="sharkDiving";
							}
						}
					}
					
					$serviziAcquistati=$elemServiziAcquistati->childNodes;
					$numServizi=$serviziAcquistati->length;
					$servizioInserito=0;
					
					if ($numServizi!=0){
						//l'utente ha precedentemente effettuato altri acquisti
						for ($j=0; $j<$numServizi; $j++){
							$servizio=$serviziAcquistati->item($j);
							$serviceId=$servizio->getAttribute("serviceId");
							if ($serviceId==$valore){
								//lo user ha gia' acquistato questo servizio quindi
								//aggiorno il numero di copie acquistate
								$quanteCopie=$servizio->getAttribute("quanteCopie");
								$quanteCopie++;
								$servizio->setAttribute("quanteCopie", "$quanteCopie");
								$servizioInserito=1;
							}	
						}
						
						if ($servizioInserito==0){	
							//lo user non ha ancora acquistato questo servizio (con questo serviceId)
								
							//creazione elem <servizio>
							$newServizio=$doc->createElement("servizio");
									
							//creazione attributo serviceId di <servizio>
							$newServizio->setAttribute("serviceId", "$valore");
									
							//creazione attributo tipoServizio di <servizio>
							$newServizio->setAttribute("tipoServizio", "$tipoServizio");
								
							//creazione attributo quanteCopie di <servizio>
							$newServizio->setAttribute("quanteCopie", "1");
									
							//inserimento di <servizio> in <serviziAcquistati>
							$elemServiziAcquistati->appendChild($newServizio);	
						}	
					}
					else {
							//e' il primo acquisto effettuato dall'utente, quindi inserisco direttamente 
							//l'acquisto
						
							//creazione elem <servizio>
							$newServizio=$doc->createElement("servizio");
								
							//creazione attributo serviceId di <servizio>
							$newServizio->setAttribute("serviceId", "$valore");
							
							//creazione attributo tipoServizio di <servizio>
							$newServizio->setAttribute("tipoServizio", "$tipoServizio");
							
							//creazione attributo quanteCopie di <servizio>
							$newServizio->setAttribute("quanteCopie", "1");
							
							//inserimento di <servizio> in <serviziAcquistati>
							$elemServiziAcquistati->appendChild($newServizio);	
					}
					$trovato=0;
				}	
			}	
		}
		
		$path=dirname(__FILE__). "/file_xml/tabellaUserCopia.xml";
		$doc->save($path);
		
		$msg.=" hai proprio speso {$_SESSION['costoCarrello']} &euro;.";	 
		$msg.="<p> La ringraziamo per l'acquisto e la informiamo che la spesa totale presso di noi: {$tot} &euro; </p>\n";
		
		//nel caso si rientri senza logout
		$_SESSION['carrello']=array();  // il carrello pagato va svuotato
		$_SESSION['costoCarrello']=0;   
		$_SESSION['spesaFinora']=$tot;		
	}		
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title> Zona Pagamenti </title>
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
				<h2> Zona pagamenti </h2>
				<?php echo $msg; ?>
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