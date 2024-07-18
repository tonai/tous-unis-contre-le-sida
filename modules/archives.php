<?php

	class Archives extends ModuleAbstrait {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


		
		function Archives() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"archives\" >";
			$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM news WHERE validation='oui'");
			$donnees = mysqli_fetch_array($buff);
			$nb_message=$donnees[0];
			if ($nb_message!=0)
			{
				/********affichage du choix des pages********/
				$messageParPage=10;
				if (!isset($_GET['page']))
				{
					$message=0;
					$pageActuelle=1;
				}
				else
				{
					$message=$messageParPage*($_GET['page']-1);
					$pageActuelle=$_GET['page'];
				}
				$pagesTotales=ceil($nb_message/$messageParPage);
				$pages=$pagesTotales;
				echo '<p>';
				if ($pageActuelle!=1)
				{
					$pagePrec=$pageActuelle-1;
					echo "\n\t<a href=\"?module=livredor&page=".$pagePrec."\" title=\"page pr�c�dante\"><</a>&nbsp&nbsp;";
				}
				echo "\n\t<a href=\"?module=livredor&page=1\" title=\"premi�re page\">1..</a>&nbsp&nbsp;";
				$i=2;
				if ($pageActuelle<=5)
				{
					$i=2;
					if ($pages>9)
						$pages=9;
				}
				elseif ($pageActuelle>=($pagesTotales-4) and $pageActuelle>5)
				{
					if ($pagesTotales>=6)
						$i=$pagesTotales-7;
				}
				else
				{
					$i=$pageActuelle-3;
					$pages=$pageActuelle+3;
				}
				for ($i;$i<$pages;$i++)
				{
					echo "\n\t<a href=\"?module=livredor&page=".$i."\">".$i."</a>&nbsp&nbsp;";
				}
				if ($pagesTotales!=1)
					echo "\n\t<a href=\"?module=livredor&page=".$pagesTotales."\" title=\"derni�re page\">..".$pagesTotales."</a>&nbsp&nbsp;";
				if ($pageActuelle!=$pagesTotales)
				{
					$pageSuiv=$pageActuelle+1;
					echo "\n\t<a href=\"?module=livredor&page=".$pageSuiv."\" title=\"page suivante\">></a>";
					}
				echo "\n<p/>";
				echo "<hr/>";
				
				
				/********affichage des messages********/
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM news WHERE validation='oui' ORDER BY id DESC LIMIT $message, $messageParPage ");
				while($donnees = mysqli_fetch_array($buff))
				{
					$date = explode('-', $donnees['date']);
					echo "<div class=\"news\">";
					echo "<h3>".$donnees['titre']." (".$date[2]."/".$date[1]."/".$date[0].")</h3>";
					$pid=$donnees['id'];
					$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM contenunews WHERE pid=$pid ORDER BY id");
					while($donnees2 = mysqli_fetch_array($buff2))
					{
						$contenu = str_replace('[lien=','<a href="',$donnees2['contenu']);
						$contenu = str_replace('[/lien]','</a>',$contenu);
						$contenu = str_replace(']','">',$contenu);
						if ($donnees2['image']!=null)
						{
							echo "<img src=\"image/".htmlentities($donnees2['image']).".jpg\" alt=\"".$donnees2['image']."\" class=\"left\" />"; 
							echo "<p>".nl2br($contenu)."</p>";
						}
						elseif ($donnees2['nombreLigne']!=null)
						{
							echo "<ul>";
							$value = explode('|', $contenu);
							for ($i=0; $i<$donnees2['nombreLigne']; $i++)
							{
								echo "<li>";
								if (isset($value[$i+1]))
									echo nl2br($value[$i+1]);
								echo "</li>";
							}
							echo "</ul>";
						}
						else
						{
							echo "<p>".nl2br($contenu)."</p>";
						}
					}
					echo "</div>";
					echo "<hr class=\"clear\"/>";
				}
				
				/********r�-affichage du choix des pages********/
				if (!isset($_GET['page']))
				{
					$message=0;
					$pageActuelle=1;
				}
				else
				{
					$message=$messageParPage*($_GET['page']-1);
					$pageActuelle=$_GET['page'];
				}
				$pagesTotales=ceil($nb_message/$messageParPage);
				$pages=$pagesTotales;
				echo '<p>';
				if ($pageActuelle!=1)
				{
					$pagePrec=$pageActuelle-1;
					echo "\n\t<a href=\"?module=livredor&page=".$pagePrec."\" title=\"page pr�c�dante\"><</a>&nbsp&nbsp;";
				}
				echo "\n\t<a href=\"?module=livredor&page=1\" title=\"premi�re page\">1..</a>&nbsp&nbsp;";
				$i=2;
				if ($pageActuelle<=5)
				{
					$i=2;
					if ($pages>9)
						$pages=9;
				}
				elseif ($pageActuelle>=($pagesTotales-4) and $pageActuelle>5)
				{
					if ($pagesTotales>=6)
						$i=$pagesTotales-7;
				}
				else
				{
					$i=$pageActuelle-3;
					$pages=$pageActuelle+3;
				}
				for ($i;$i<$pages;$i++)
				{
					echo "\n\t<a href=\"?module=livredor&page=".$i."\">".$i."</a>&nbsp&nbsp;";
				}
				if ($pagesTotales!=1)
					echo "\n\t<a href=\"?module=livredor&page=".$pagesTotales."\" title=\"derni�re page\">..".$pagesTotales."</a>&nbsp&nbsp;";
				if ($pageActuelle!=$pagesTotales)
				{
					$pageSuiv=$pageActuelle+1;
					echo "\n\t<a href=\"?module=livredor&page=".$pageSuiv."\" title=\"page suivante\">></a>";
					}
				echo "\n<p/>";
			}
			else
			{
				echo "<p>La setion archive est vide.</p>";
			}
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=archives\" >Les archives</a>";
			echo "</p>";
			echo "</div>";
		}
		
		
	}

?>