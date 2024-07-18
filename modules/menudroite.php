<?php

	class MenuDroite extends ModuleAbstrait {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


	
		function MenuDroite() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
		}
		
		
		
		function afficherMenuDroite() {

?>
		<div class="bandeau" >
			Acc�s rapide
		</div>
		<div id="accesRapide" >
			<ul>
<?php
			$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM accesrapide ");
			$donnees = mysqli_fetch_array($buff);
			if ($donnees[0]!=0)
			{
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM accesrapide ORDER BY ordre ");
				while ($donnees = mysqli_fetch_array($buff))
				{
					if ($donnees['dir']==null)
						echo "<li><a href=\"?module=".$donnees['module']."\" >".$donnees['texte']."</a></li>";
					else
						echo "<li><a href=\"?module=".$donnees['module']."&dir=".$donnees['dir']."\" >".$donnees['texte']."</a></li>";
				}
			}

?>
			</ul>
		</div>
<?php

		}
		
		
		
		function navigation() {
		}
		
	}

?>