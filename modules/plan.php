<?php

	class Plan extends ModuleAbstrait {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


		
		function Plan() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"plan\">";
			echo "<ul>";
			$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM menu WHERE pid=0 ORDER BY uid ASC");
			while($donnees = mysqli_fetch_array($buff)) {
				echo "\n<li><a href=\"index.php?module=".$donnees['lien']."\">".$donnees['menu']."</a>";
				$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM menu WHERE pid='$donnees[uid]' ORDER BY uid ASC");
				$donnees2 = mysqli_fetch_array($buff2);
				if ($donnees2['uid']!="") {
					echo "\n\t<ul>";
					echo "\n\t\t<li><a href=\"index.php?module=".$donnees2['lien']."\">".$donnees2['menu']."</a></li>";
					while($donnees2 = mysqli_fetch_array($buff2)) {
						
						echo "\n\t\t<li><a href=\"index.php?module=".$donnees2['lien']."\">".$donnees2['menu']."</a></li>";
						
					}
					echo "\n\t</ul>\n";
				}
				echo "</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=plan\" >Plan du site</a>";
			echo "</p>";
			echo "</div>";
		}
		
	}