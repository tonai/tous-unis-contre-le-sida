<?php

	class DisplayManager {
	
		var $enteteHTML;
		var $header;
		var $footer;
		var $moduleManager;
		
		function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;

			require_once("EnteteHTML.php");
			require_once("Header.php");
			require_once("Footer.php");
			require_once("lib/ModuleManager.php");
			
			$this->enteteHTML = new EnteteHTML();
			$this->header = new Header($this->baseDeDonnees);
			$this->footer = new Footer();
			$this->moduleManager = new ModuleManager($this->baseDeDonnees);
			
			if(isset($_GET['module'])) {
				$this->moduleManager->loadModulesFromDb($_GET['module']);
			}
			else {
				$this->moduleManager->loadModulesFromDb("accueil");
			}
			
			if(isset($_GET['module']) && isset($_GET['action']))
			{
				$this->moduleManager->preTraitement($_GET['module'], $_GET['action']);
			}
		}
	
		function display() {
			$this->enteteHTML->afficher($this->moduleManager->modules);
	
?>
	<body>
		
		<div id="header">
<?php

			$this->header->afficher();

?>
		</div>
		<div id="page">
			<div id="menuDroite">
<?php

			foreach($this->moduleManager->modules as $module){
		        $module->afficherMenuDroite();
		    }

?>
				
			</div>
			<div id="corps">
<?php

			if (isset($_GET['module']))
				$this->moduleManager->modules[$_GET['module']]->afficherPage();
			else
		        $this->moduleManager->modules['accueil']->afficherPage();
			echo "<div id=\"footer\">";
			$this->footer->afficher();

?>
				</div>
			</div>
		</div>
		<script language="javascript">
			defilimg();
		</script>
	</body>
</html>
<?php

		}
		
	}