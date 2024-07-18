<?php

	class News extends ModuleAbstrait {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }
		
		function News() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"news\" >";
			$date = date("Y-m-d", time()-(30*24*60*60));
			$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM news WHERE validation='oui' AND date>='$date'");
			$donnees = mysqli_fetch_array($buff);
			if ($donnees[0]!=0)
			{
				echo "<h1>News des 30 derniers jours</h1>";
				echo "<hr/>";
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM news WHERE validation='oui' AND date>='$date' ORDER BY id DESC");
				while ($donnees = mysqli_fetch_array($buff))
				{
					$date = explode('-', $donnees['date']);
					echo "<h3>".$donnees['titre']." (".$date[2]."/".$date[1]."/".$date[0].")</h3>";
					$pid=$donnees['id'];
					$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM contenunews WHERE pid=$pid ORDER BY id ");
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
					echo "<hr/>";
				}
			}
			else
			{
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM news WHERE validation='oui'");
				$donnees = mysqli_fetch_array($buff);
				if ($donnees[0]!=0)
				{
					$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM news WHERE validation='oui' ORDER BY date DESC LIMIT 0,1");
					$donnees = mysqli_fetch_array($buff);
					$date = explode('-', $donnees['date']);
					echo "<h1>Derni�re news (".$date[2]."/".$date[1]."/".$date[0].")</h1>";
					echo "<h3>".$donnees['titre']."</h3>";
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
				}
				else
				{
					echo "Il n'y a aucune news d'enregistr�e.";
				}
			}
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=news\" >Les news</a>";
			echo "</p>";
			echo "</div>";
		}
		
		
	}

?>