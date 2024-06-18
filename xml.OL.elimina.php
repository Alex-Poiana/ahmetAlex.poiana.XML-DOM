<?php
	error_reporting (E_ALL &~E_NOTICE);

	session_start();               

	if (!isset($_SESSION['accessoPermesso']) || $_SESSION['accessoPermesso']!="utente") 
	   header('Location: xml.OL.login.php');
   
   $msg="";
   $msgCosto="";
   $tabellaCarrello="";
   $trovato=0;
   
	if (!isset($_SESSION['carrello']) || ($_SESSION['costoCarrello']==0)) {
		$msg.= "<p> - Carrello vuoto - </p>\n";
	} 
	else {
			$msg.="<p> Seleziona quel che vuoi eliminare dal carrello: </p>\n";
			
			//creazione tabella che mostra il contenuto del carrello
			$tabellaCarrello.="<table border=\"2px\" cellspacing=\"2px\" cellpadding=\"3px\" style=\"border-color: fuchsia\">\n";
			$tabellaCarrello.="<thead>\n <tr>\n <th></th>\n <th> Service Id </th>\n <th> Nome Servizio </th>\n <th> Data </th>\n <th> Costo </th>\n </tr>\n</thead>\n\n<tbody>\n";
			
			foreach ($_SESSION['carrello'] as $chiave=>$valore){
				//controllo se l'elemento del carrello e' un servizio whaleWatch
				$xmlString="";
				foreach (file("file_xml/tabellaWhaleWatchCopia.xml") as $node)
					$xmlString.= trim($node);
	
				$doc=new DOMDocument();
				$doc->loadXML($xmlString);
		
				$root=$doc->documentElement;
				$elementi=$root->childNodes;
				
				for ($i=0; $i<$elementi->length; $i++){
					$elemento=$elementi->item($i);
					$serviceId=$elemento->getAttribute("serviceId");
					if ($serviceId==$valore){
						//si tratta di un servizio whaleWatch quindi lo memorizzo in una
						//variabile che poi usero' per stampare il contenuto del carrello
						$servizioCarrello=$elemento;
						$trovato=1;
					}	
				}
				
				//controllo se l'elemento del carrello e' un servizio dolphinSwim
				if ($trovato!=1){
					$xmlString="";
					foreach (file("file_xml/tabellaDolphinSwimCopia.xml") as $node)
						$xmlString.= trim($node);
		
					$doc=new DOMDocument();
					$doc->loadXML($xmlString);
			
					$root=$doc->documentElement;
					$elementi=$root->childNodes;
					
					for ($i=0; $i<$elementi->length; $i++){
						$elemento=$elementi->item($i);
						$serviceId=$elemento->getAttribute("serviceId");
						if ($serviceId==$valore){
							//si tratta di un servizio dolphinSwim quindi lo memorizzo in una
							//variabile che poi usero' per stampare il contenuto del carrello
							$servizioCarrello=$elemento;
							$trovato=1;
						}	
					}
				}
				
				//controllo se l'elemento del carrello e' un servizio sharkDive
				if ($trovato!=1){
					$xmlString="";
					foreach (file("file_xml/tabellaSharkDiveCopia.xml") as $node)
						$xmlString.= trim($node);
		
					$doc=new DOMDocument();
					$doc->loadXML($xmlString);
			
					$root=$doc->documentElement;
					$elementi=$root->childNodes;
					
					for ($i=0; $i<$elementi->length; $i++){
						$elemento=$elementi->item($i);
						$serviceId=$elemento->getAttribute("serviceId");
						if ($serviceId==$valore){
							//si tratta di un servizio sharkDive quindi lo memorizzo in una
							//variabile che poi usero' per stampare il contenuto del carrello
							$servizioCarrello=$elemento;
						}		
					}
				}
				
				//recupero le info da stampare contenute in $servizioCarrello
				$nomeServizio=$servizioCarrello->firstChild->textContent;
				$data=$servizioCarrello->getElementsByTagName("data")->item(0)->textContent;
				$costo=$servizioCarrello->lastChild->textContent;
				
				$tabellaCarrello.="<tr>\n <td> <input type=\"checkbox\" name=\"eliminandi[]\" value=\"$chiave\" /> </td>\n";
				$tabellaCarrello.=" <td> $valore </td>\n <td> $nomeServizio </td>\n <td> $data </td>\n <td> $costo&euro; </td>\n</tr>\n\n";
				$trovato=0;
			}

			$tabellaCarrello.="</tbody>\n </table>\n";
				
			if ($_SESSION['costoCarrello']!=0)
					$msgCosto.="<p> Costo totale del carrello: {$_SESSION['costoCarrello']}&euro; </p>\n";
			
			//$trovato=0;
			
			if (isset($_POST['eliminandi'])) {
				//bisogna eliminare le cose selezionate in eliminandi[] e
				//togliere da $_SESSION['costoCarrello'] il costo degli elementi eliminati
				foreach ($_POST['eliminandi'] as $k=>$indiceDaEliminare){
					$id=$_SESSION['carrello'] [$indiceDaEliminare];
					
					//controllo se l'elemento da eliminare e' un servizio whaleWatch
					$xmlString="";
					foreach (file("file_xml/tabellaWhaleWatchCopia.xml") as $node)
						$xmlString.= trim($node);
		
					$doc=new DOMDocument();
					$doc->loadXML($xmlString);
			
					$root=$doc->documentElement;
					$elementi=$root->childNodes;
					
					for ($i=0; $i<$elementi->length; $i++){
						$elemento=$elementi->item($i);
						$serviceId=$elemento->getAttribute("serviceId");
						if ($serviceId==$id){
							//si tratta di un servizio whaleWatch quindi ne ricavo il costo
							$costo=$elemento->lastChild->textContent;
							$trovato=1;
						}	
					}
					
					//controllo se l'elemento da eliminare e' un servizio dolphinSwim
					if ($trovato!=1){
						$xmlString="";
						foreach (file("file_xml/tabellaDolphinSwimCopia.xml") as $node)
							$xmlString.= trim($node);
			
						$doc=new DOMDocument();
						$doc->loadXML($xmlString);
				
						$root=$doc->documentElement;
						$elementi=$root->childNodes;
						
						for ($i=0; $i<$elementi->length; $i++){
							$elemento=$elementi->item($i);
							$serviceId=$elemento->getAttribute("serviceId");
							if ($serviceId==$id){
								//si tratta di un servizio dolphinSwim quindi ne ricavo il costo
								$costo=$elemento->lastChild->textContent;
								$trovato=1;
							}	
						}
					}
					
					//controllo se l'elemento da eliminare e' un servizio sharkDive
					if ($trovato!=1){
						$xmlString="";
						foreach (file("file_xml/tabellaSharkDiveCopia.xml") as $node)
							$xmlString.= trim($node);
			
						$doc=new DOMDocument();
						$doc->loadXML($xmlString);
				
						$root=$doc->documentElement;
						$elementi=$root->childNodes;
						
						for ($i=0; $i<$elementi->length; $i++){
							$elemento=$elementi->item($i);
							$serviceId=$elemento->getAttribute("serviceId");
							if ($serviceId==$id){
								//si tratta di un servizio sharkDive quindi ne ricavo il costo
								$costo=$elemento->lastChild->textContent;
							}		
						}
					}
					
					//tolgo dal costo del carrello il costo del servizio eliminato
					$_SESSION['costoCarrello']-=$costo;	
					$trovato=0;

					unset($_SESSION['carrello'][$indiceDaEliminare]);
				}
				header('Location: xml.OL.elimina.php');
			}		
	}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title> Elimina servizi </title>
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
				<h2> Eliminazione Servizi </h2>
				<?php echo $msg; ?>

				<form action="<?php $_SERVER['PHP_SELF']?>"  method="post" >
					<table>
						<tr>
							<td  style="width: 30%">
								<p style="margin-bottom: 15%">
									<input type="reset" name="annulla" value="Annulla le selezioni" />
								</p>
								
								<p style="margin-top: 15%">
									<input type="submit" name="cancellaSelezionati" value="Cancella i selezionati" />
								</p>
							</td>

							<td> <?php echo $tabellaCarrello; ?> </td>
						</tr>
					</table>
				</form>
				
				<?php echo $msgCosto; ?>
				
				<hr />
			
				<!-- inizio parte per visualizzare/controllare il contenuto di $_SESSION e $_POST -->
				<table>
					<tr>
						<td style="width: 50%">
							<?php 
								echo "\$_SESSION:<br />";
								foreach ($_SESSION as $k=>$v){
									if ($k!="carrello")	
										echo "[".$k."]  ".$v."\n<br />";
								}
							?>
						</td>
						
						<td style="width: 50%">
							<?php
								echo "\$_POST:<br />";
								foreach ($_POST as $k=>$v)
								  echo "[$k] $v<br />";
							?>
						</td>
					</tr>
				</table>
				<!-- fine parte per visualizzare/controllare il contenuto di $_SESSION e $_POST -->
				<hr />
				
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