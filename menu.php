<?php 
//Tentativo di modularizzazione: il menu` dell'applicazione web che stiamo costruendo e` 
//centralizzato qui, cosi` le modifiche saranno valide per tutti gli script appena fatte in 
//questo file 

	//menu Utente
	$menuUtente=<<<'UTN'
<div id="menu">
					<ul>
						<li><a href="xml.OL.servizi.php"> Aggiungi servizi al carrello </a></li>
						<li><a href="xml.OL.elimina.php"> Elimina servizi dal carrello </a></li>
						<li><a href="xml.OL.pagamento.php"> Effettua Pagamento </a></li>
					</ul>
				</div>
UTN;

	//menu Admin
	$menuAdmin=<<<'ADM'
<div id="menu">
					<ul>
						<li><a href="xml.OL.elencoUtenti.php"> Visualizzazione elenco utenti </a></li>
						<li><a href="xml.OL.gestioneBan.php"> Gestione Ban/Sban utenti</a></li>
						<li><a href="xml.OL.gestioneGestori.php"> Gestione gestori </a></li>
					</ul>
				</div>
ADM;

	//menu Gestore
	$menuGestore=<<<'GST'
<div id="menu">
					<ul>
						<li><a href="xml.OL.aggiungiServizi.php"> Aggiunta nuovi servizi </a></li>
						<li><a href="xml.OL.acquistiUtente.php"> Visualizzazione elenco acquisti utente </a></li>
					</ul>
				</div>
GST;
	
	if (!isset($_SESSION['accessoPermesso'])) 
	  header('Location: xml.OL.login.php');
	else {
		switch ($_SESSION['accessoPermesso']){
		
			case "utente": 
				echo $menuUtente;
			break;
		
			case "admin": 
				echo $menuAdmin;
			break;
		
			case "gestore": 
				echo $menuGestore;
			break;
		}
	}
?>

