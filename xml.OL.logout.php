<?php
	session_start();
	unset($_SESSION);
	session_destroy();
	header('Location: Home.html');
	/*header('Location: xml.OL.login.php');*/
?>
