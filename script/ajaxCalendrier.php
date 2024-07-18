<?php
	include ("../admin/config.php");
	if(isset($_POST['date']))
	{
		$date=$_POST['date'];
		$this-> mysqli = mysqli_connect($db['hostName'], $db['userName'], $db['password']);
		mysqli_select_db($this-> mysqli, $db['dataBase']);
		$reponse=mysqli_query($this-> mysqli, "SELECT * FROM evenement WHERE date='$date'");
		$donnees=mysqli_fetch_array($reponse);
		$evenement=$donnees['nom'];
		$descriptionEvenement=nl2br($donnees['description']);
		$passageEvenement=$donnees['passage'];
		$lieuEvenement=$donnees['lieu'];
		$adresseEvenement=nl2br($donnees['adresse']);
		$lienEvenement=$donnees['lien'];
		mysqli_close($this-> mysqli);
		$date=explode("-", $date);
		$annee=$date[0];
		$mois=$date[1];
		$jour=$date[2];
		$passageEvenement=explode(":", $passageEvenement);
		$heure=$passageEvenement[0];
		$minute=$passageEvenement[1];
		$seconde=$passageEvenement[2];
		
		echo "\n<h3><strong>".$evenement."</strong></h3>";
		
		echo "\n<strong>-- date --</strong><br/>";
		echo "le ".$jour."/".$mois."/".$annee."<br/>";
		echo "\n<hr/>";
		
		echo "\n<strong>-- description --</strong><br/>";
		if ($descriptionEvenement!="")
			echo $descriptionEvenement."<br/>";
		else
			echo "non renseign&eacute;e<br/>";
		echo "\n<hr/>";
			
		echo "\n<strong>-- horaire de passage --</strong><br/>";
		if ($seconde==00)
			echo $heure."H".$minute."<br/>";
		else
			echo "non renseign&eacute;e<br/>";
		echo "\n<hr/>";
			
		echo "\n<strong>-- lieu --</strong><br/>";
		if ($lieuEvenement!="")
			echo $lieuEvenement."<br/>";
		else
			echo "non renseign&eacute;e<br/>";
		echo "\n<hr/>";
			
		echo "\n<strong>-- adresse --</strong><br/>";
		if ($adresseEvenement!="")
			echo $adresseEvenement."<br/>";
		else
			echo "non renseign&eacute;e<br/>";
		echo "\n<hr/>";
			
		echo "\n<strong>-- lien --</strong><br/>";
		if ($lienEvenement!="")
			echo "<a href=\"".$lienEvenement."\">".$lienEvenement."</a><br/>";
		else
			echo "non renseign&eacute;e<br/>";
	}
?>