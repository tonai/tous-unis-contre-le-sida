<?php

	class EnteteHTML {
		var $charset = "charset=iso-8859-1";
		var $auteur = "Tony CABAYE";
		var $description = "Site de l'association Tous unis contre le SIDA";
		var $keywords = "association, SIDA, VIH, prèvention";
		var $path = "style/";
		var $css = "style.css";
	
		function EnteteHTML() {
		}
		
		
		
		function afficher($modules) {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Tous unis contre le SIDA</title>
		<meta http-equiv="Content-Type" content="text/html; <?php echo $this->charset; ?>" />
		<meta name="author" content="<?php echo $this->auteur; ?>" />
		<meta name="description" content="<?php echo $this->description; ?>" />
		<meta name="keywords" content="<?php echo $this->keywords; ?>" />
		<link rel="stylesheet" media="screen" type="text/css" title="Style" href="<?php echo $this->path.$this->css; ?>" />
		<!--[if lte IE 7]>
			<style type="text/css">
				#header div > ul {
					margin-top: 27px;
				}
			</style>
		<![endif]-->
	
<?php
			$path="script";
			$dossier=opendir($path);
			while ($file=readdir($dossier))
			{
				if ($file!="." && $file!="..")
				{
					$n=strlen($file)-3;
					$extension=substr($file, $n, 3);
					if ($extension==".js")
					{
						$nom=substr($file, 0, $n);
						foreach ($modules as $module )
						{
							if ($nom==$module->getName())
							{
								echo "<script type=\"text/javascript\" src=\"script/".$nom.".js\"></script>";
							}
						}
					}
				}
			}
			closedir($dossier);
			echo "</head>";
		}
	}

?>