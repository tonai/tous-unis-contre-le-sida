<?php

	class Header {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


	
		function Header() {
		}
		
		
		
		function afficher() {

?>
			<div>
				<a href="?module=accueil" ><img src="style/logo.jpg" alt="logo" /></a>
				<strong>Tous Unis Contre Le SIDA</strong>
				<ul>
<?php

	$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM menu WHERE pid=0 ORDER BY uid ASC");
	while($donnees = mysqli_fetch_array($buff)) {
		if ($donnees['lien']!=null)
			echo "\n<li><a href=\"index.php?module=".$donnees['lien']."\">".$donnees['menu']."</a>";
		else
			echo "\n\t\t<li><a href=\"#\">".$donnees['menu']."</a>";
		$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM menu WHERE pid='$donnees[uid]' ORDER BY uid ASC");
		$donnees2 = mysqli_fetch_array($buff2);
		if ($donnees2['uid']!="") {
			echo "\n\t<ul>";
			if ($donnees2['lien']!=null)
				echo "\n\t\t<li><a href=\"index.php?module=".$donnees2['lien']."\">".$donnees2['menu']."</a></li>";
			else
				echo "\n\t\t<li><a href=\"#\">".$donnees2['menu']."</a></li>";
			while($donnees2 = mysqli_fetch_array($buff2)) {
				if ($donnees2['lien']!=null)
					echo "\n\t\t<li><a href=\"index.php?module=".$donnees2['lien']."\">".$donnees2['menu']."</a></li>";
				else
					echo "\n\t\t<li><a href=\"#\">".$donnees2['menu']."</a></li>";
			}
			echo "\n\t</ul>\n";
		}
		echo "</li>";
	}
?>
				</ul>
			</div>
<?php

		}
	}

?>