<?php

	class Livredor extends ModuleAbstrait {
		
		var $erreurNom="";
		var $erreurMessage="";
		var $enregistrement="";

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


		
		function Livredor() {
		}
		
		
		
		function preTraitement($action) {
			switch($action)
			{
				case 'ecrire':
					if (isset($_POST['nom']))
					{
						if ($_POST['nom']!=null and $_POST['message']!=null)
						{
							$nom=htmlentities($_POST['nom'], ENT_QUOTES);
							$message=htmlentities($_POST['message'], ENT_QUOTES);
							mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO livredor VALUES('', '$nom', '$message')");
							$this->erreurNom="";
							$this->erreurMessage="";
							$this->enregistrement="Votre message � bien �t� enregistr�.";
						}
						elseif ($_POST['nom']==null && $_POST['message']==null)
						{
							$this->erreurNom="Aucun nom n'a �t� renseign�.";
							$this->erreurMessage="Aucun massage n'a �t� renseign�.";
							$this->enregistrement="";
						}
						elseif ($_POST['nom']==null)
						{
							$this->erreurNom="Aucun nom n'a �t� renseign�.";
							$this->erreurMessage="";
							$this->enregistrement="";
						}
						elseif ($_POST['message']==null)
						{
							$this->erreurNom="";
							$this->erreurMessage="Aucun massage n'a �t� renseign�.";
							$this->enregistrement="";
						}
					}
					break;
				
				case 'suppression':
					if ($_SESSION['connect'])
					{
						$reponse=mysqli_query($this->baseDeDonnees->mysqli, 'SELECT id FROM livredor');
						while($donnees=mysqli_fetch_array($reponse))
						{
							$id=$donnees['id'];
							if(isset($_POST[$id]))
							{
								if($_POST[$id]=='on')
									mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM livredor WHERE id=$id") OR DIE (mysqli_error());
							}
						}
					}
					break;
					
				default:
					break;
			}
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"livredor\">";
			
			$action=false;
			if (isset($_GET['action']))
			{
				if($_GET['action']=="ecrire")
					$action=true;
			}
			
			if ($action)
			{
?>
				<form method="post" action="?module=livredor&action=ecrire">
					<fieldset>
						<legend>Ecrire un message dans le livre d'or</legend>
							<p>
								<label for="nom" >Votre nom : </label>
								<input type="text" name="nom" value="<?php if(isset($_POST['nom']) && $this->enregistrement=="") echo $_POST['nom']; ?>" />
								<span class="error" ><?php echo $this->erreurNom; ?></span>
							</p>
							<p>
								<label for="nom" >Votre message : </label>
								<span class="error" ><?php echo $this->erreurMessage; ?></span><br/>
								<textarea name="message" ><?php if(isset($_POST['message']) && $this->enregistrement=="") echo $_POST['message']; ?></textarea>
							</p>
								<input type="submit"/>
								<input type ="reset" /><br/>
								<span class="error" ><?php echo $this->enregistrement; ?></span>
							</p>
					</fieldset>
				</form>
<?php
			}
			else
			{
				$reponse=mysqli_query($this->baseDeDonnees->mysqli, "SELECT COUNT(*) AS nb_messages FROM livredor");
				$donnees=mysqli_fetch_row($reponse);
				$nb_message=$donnees[0];
				
				echo "<p class=\"left\" ><a href=\"?module=livredor&action=ecrire\" >Ecrire un message</a></p>";
				echo "<p class=\"right\" ><a href=\"?module=livredor&action=ecrire\" >Ecrire un message</a></p>";
				
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
					echo "<hr class=\"clear\" />";
					
					
					/********affichage des messages********/
					if ($_SESSION['connect'])
					{
						echo "<form action=\"?module=livredor&action=suppression\" method=\"post\">";
					}
					$reponse=mysqli_query($this->baseDeDonnees->mysqli, 'SELECT * FROM livredor ORDER BY id DESC LIMIT '.$message.','.$messageParPage.' ');
					while($donnees=mysqli_fetch_array($reponse))
					{
						echo "\n<dl>";
						echo "\n<dt>";
						if ($_SESSION['connect']==1)
						{
							echo "<input type=\"checkbox\" name=\"".$donnees['id']."\"/>";
						}
						echo "<strong>".nl2br(stripslashes($donnees['nom']))." :</strong></dt>";
						echo "\n\t<dd>".nl2br(stripslashes($donnees['message']))."</dd>";
						echo "\n</dl>";
					}
					if ($_SESSION['connect'])
					{
						echo "<p><input type =\"submit\" value=\"supprimer les messages coch�s\" /><input type =\"reset\" /></p>";
						echo "</form>";
					}
					
					
					echo "<hr />";
					echo "<p class=\"left\" ><a href=\"?module=livredor&action=ecrire\" >Ecrire un message</a></p>";
					echo "<p class=\"right\" ><a href=\"?module=livredor&action=ecrire\" >Ecrire un message</a></p>";
					
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
					echo "<p>Il n'y a pas encore de message enregistr�.</p>";
				}
			}
			echo "</div>";
			
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=livredor\" >Livre d'or</a>";
			if (isset($_GET['action']))
			{
				if ($_GET['action']=="ecrire")
				{
					echo " > ";
					echo "<a href=\"?module=livredor&action=ecrire\" >Ecrire un message</a>";
				}
			}
			echo "</p>";
			echo "</div>";
		}
		
	}