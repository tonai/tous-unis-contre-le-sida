<?php

	class EditeurTexte {

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


	
		function EditeurTexte() {
		}
		
		
		
		function preTraitement($module, $action) {
			$erreur="";
			switch($action)
			{
				case 'contenu':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM $module");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id=$donnees['id'];
							if ($donnees['nombreLigne']!=null)
							{
								$contenu="";
								for ($j=0; $j<$donnees['nombreLigne']; $j++)
								{
									if (isset($_POST[$id."/".$j]))
									{
										$contenu.="|".str_replace('|', '', $_POST[$id."/".$j]);
									}
								}
								if ($contenu!="")
								{
									$contenu=htmlentities($contenu, ENT_QUOTES);
									mysqli_query($this->baseDeDonnees->mysqli, "UPDATE $module SET contenu = '$contenu' WHERE id = $id") OR DIE (mysqli_error());
								}
							}
							else
							{
								if (isset($_POST[$id]))
								{
									$contenu=htmlentities($_POST[$id], ENT_QUOTES);
									mysqli_query($this->baseDeDonnees->mysqli, "UPDATE $module SET contenu = '$contenu' WHERE id = $id") OR DIE (mysqli_error());
								}
							}
							if (isset($_POST['sup/'.$id]))
							{
								if($_POST['sup/'.$id]=='on')
								{
									mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM $module WHERE id = $id") OR DIE (mysqli_error());
									if ($donnees['image']!=null)
									{
										$directory="image";
										$dossier=opendir($directory);
										while ($file=readdir($dossier))
										{
											$path=$directory."/".$file;
											if ($donnees['image'].".jpg"==$file)
												unlink($path);
										}
										closedir($dossier);
									}
								}
							}
						}
					}
					break;
				
				case 'ajoutElement':
					if ($_SESSION['connect'])
					{
						if (isset($_POST['paragraphe']))
						{
							if ($_POST['paragraphe']=="on")
							{
								mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO $module VALUES('', '', null, null)");
							}
						}
						if (isset($_POST['paragrapheImage']))
						{
							if ($_POST['paragrapheImage']=="on")
							{
								if (isset($_FILES['image']['error']))
								{
									$erreur = "Une erreur s'est produite";
									if ($_FILES['image']['error'] > 0)
										$erreur = "Erreur lors du tranfsert";
									else
									{
										$extension = 'jpg';
										$nomFichier = explode('.', strtolower($_FILES['image']['name']));
										if ($extension == $nomFichier[1]) //si fichier ".jpg"
										{
											$nom = htmlentities($nomFichier[0]);
											$direction = "image/".$nom.".".$nomFichier[1];
											if (move_uploaded_file($_FILES['image']['tmp_name'],$direction))
											{
												$erreur = "Transfert r�ussi";
												mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO $module VALUES('', '', '$nom', null)");
											}
											else
												$erreur = "Erreur lors du tranfsert";
										}
									}
								}
							}
						}
						if (isset($_POST['liste']))
						{
							if ($_POST['liste']=="on")
							{
								$nombreLigne = $_POST['nombreLigne'];
								mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO $module VALUES('', '', null, '$nombreLigne')");
							}
						}
					}
					break;
				
				default:
					break;
			}
			return $erreur;
		}
		
		
		
		function afficherPage($module) {
			if ($_SESSION['connect'])
			{

?>
				<form method="post" action="?module=<?php echo $module; ?>&action=ajoutElement" enctype="multipart/form-data" >
					<fieldset>
						<legend>Ajouter un �l�ment</legend>
						<p>
							<input type="checkbox" name="paragraphe" /><label for="paragraphe" > Ajouter un paragraphe simple</label>
						</p>
						<p>
							<input type="checkbox" name="paragrapheImage" /><label for="paragrapheImage" > Ajouter un paragraphe avec image : </label><input type="file" name="image"/>
						</p>
						<p>
							<input type="checkbox" name="liste" /><label for="liste" > Ajouter une liste � </label><input type="text" value="3" name="nombreLigne" style="width: 30px;" /><label for="nombreLigne" > �l�ments.</label>
						</p>
						<input type="submit" />
						<input type ="reset" />
					</fieldset>
				</form>
				<p>
					Pour ajouter un lien, suivez le mod�le suivant : [lien=l_adresse_du_lien]texte_personnalis�[/lien].
				</p>
				<ul>
					<li>Par exemple : Voici le lien vers [lien=http://www.tousuniscontrelesida.fr]notre site[/lien].</li>
					<li>Donnera r�ellement : Voici le lien vers <a href="http://www.tousuniscontrelesida.fr">notre site</a>.</li>
				</ul>
				<form method="post" action="?module=<?php echo $module; ?>&action=contenu" >
					<fieldset>
						<legend>Modifier le contenu</legend>
						<div id="elements" >
<?php

				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM $module ORDER BY id");
				while($donnees = mysqli_fetch_array($buff))
				{
					if ($donnees['image']!=null)
					{
						echo "paragraphe avec image image : <img src=\"image/".htmlentities($donnees['image']).".jpg\" alt=\"".$donnees['image']."\" width=\"100\" /><br/>"; 
						echo "<textarea name=\"".$donnees['id']."\" >".$donnees['contenu']."</textarea><br/>";
						echo "<input type=\"checkbox\" name=\"sup/".$donnees['id']."\" /> supprimer";
					}
					elseif ($donnees['nombreLigne']!=null)
					{
						echo "liste � ".$donnees['nombreLigne']." �l�ments :";
						echo "<ul>";
						$value = explode('|', $donnees['contenu']);
						for ($i=0; $i<$donnees['nombreLigne']; $i++)
						{
							echo "<li><input name=\"".$donnees['id']."/".$i."\" ";
							if (isset($value[$i+1]))
								echo "value=\"".$value[$i+1]."\" ";
							echo "style=\"width:600px;\" /></li>";
						}
						echo "</ul><br/>";
						echo "<input type=\"checkbox\" name=\"sup/".$donnees['id']."\" /> supprimer";
					}
					else
					{
						echo "paragraphe simple :<br/>";
						echo "<textarea name=\"".$donnees['id']."\" >".$donnees['contenu']."</textarea><br/>";
						echo "<input type=\"checkbox\" name=\"sup/".$donnees['id']."\" /> supprimer";
					}
					echo "<hr/>";
				}

?>
							</div>
							<input type="submit"/>
							<input type ="reset" />
					</fieldset>
				</form>
<?php

			}
			else
			{
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM $module ORDER BY id");
				while($donnees = mysqli_fetch_array($buff))
				{
					$contenu = str_replace('[lien=','<a href="',$donnees['contenu']);
					$contenu = str_replace('[/lien]','</a>',$contenu);
					$contenu = str_replace(']','">',$contenu);
					if ($donnees['image']!=null)
					{
						echo "<img src=\"image/".htmlentities($donnees['image']).".jpg\" alt=\"".$donnees['image']."\" class=\"left\" />"; 
						echo "<p>".nl2br($contenu)."</p>";
					}
					elseif ($donnees['nombreLigne']!=null)
					{
						echo "<ul>";
						$value = explode('|', $contenu);
						for ($i=0; $i<$donnees['nombreLigne']; $i++)
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
						echo "<p>".nl2br($donnees['contenu'])."</p>";
					}
				}
			}
		}
		
	}

?>