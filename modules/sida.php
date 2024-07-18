<?php

	class Sida extends ModuleAbstrait {
		
		var $erreur = "";	
		var $editeurTexte;
		
		function Sida() {
			include_once("lib/EditeurTexte.php");
			$this->editeurTexte = new EditeurTexte();
		}
		
		
		
		function preTraitement($action) {
			$this->erreur = $this->editeurTexte->preTraitement($this->getName(), $action);
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"sida\" >";
			$this->editeurTexte->afficherPage($this->getName());
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=sida\" >Le SIDA</a>";
			echo "</p>";
			echo "</div>";
		}
		
		
	}

?>