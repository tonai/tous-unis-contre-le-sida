<?php

	class ModuleAbstrait {
	
		function ModuleAbstrait() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
		}
		
		
		
		function getName() {
			return strtolower(get_class($this));
	    } 
		
	}

?>