<?php

	class Login extends ModuleAbstrait {
	
		var $erreur = "";
		var $creation = "";
		var $login = "";
		var $password = "";
		var $mail = "";

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


	
		function Login() {
		}
		
		
		
		function preTraitement($action) {
			$this->creation="";
			$this->login = "";
			$this->password = "";
			$this->mail = "";
			switch($action) {
				case 'connexion':
					$login=$_POST['login'];
					$password=md5($_POST['password']);
					$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, login, password FROM utilisateurs");
					while($donnees = mysqli_fetch_array($buff))
					{
						if ($login==$donnees['login'] && $password==$donnees['password'])
						{
							$_SESSION['connect']=1;
							$_SESSION['id']=$donnees['id'];
							$_SESSION['login']=$login;
							$_SESSION['password']=$password;
							header('Location: index.php?module=accueil');
						}
					}
					break;
					
				case 'deconnexion':
					if (isset($_POST['deconnexion']))
					{
						$_SESSION['connect']=0;
						$_SESSION['id']='';
						$_SESSION['login']='';
						$_SESSION['password']='';
					}
					header('Location: index.php?module=accueil');
					break;
					
				case 'ajoutPhotos':
					if ($_SESSION['connect'])
					{
						if (isset($_FILES['photo']['error']))
						{
							$this->erreur = "Une erreur s'est produite";
							if ($_FILES['photo']['error'] > 0)
								$this->erreur = "Erreur lors du tranfsert";
							else
							{
								$extension = 'jpg';
								$nomFichier = explode('.', strtolower($_FILES['photo']['name']));
								if ($extension == $nomFichier[1]) //si fichier ".jpg"
								{
									$direction = "galerie/".$_POST['dir']."/".htmlentities($nomFichier[0]).".".$nomFichier[1];
									if (move_uploaded_file($_FILES['photo']['tmp_name'],$direction))
										$this->erreur = "Transfert r�ussi";
									else
										$this->erreur = "Erreur lors du tranfsert";
								}
							}
						}
					}
					break;
				
				case 'ajoutDossier':
					if ($_SESSION['connect'])
					{
						mkdir("galerie/".$_POST['dir']);
					}
					break;
					
				case 'ajoutEvent':
					if ($_SESSION['connect'])
					{
						$jour=1;
						$mois=1;
						$an=date("Y");
						$annee=$an;
						$heure=0;
						$minute=0;
						if (isset($_POST['nom']))
						{
							$jour=$_POST['jour'];
							$mois=$_POST['mois'];
							$annee=$_POST['annee'];
							$heure=$_POST['heure'];
							$minute=$_POST['minute'];
							if($_POST['nom']!="")
							{
								$nom=htmlentities($_POST['nom'], ENT_QUOTES);
								$date=$annee.'-'.$mois.'-'.$jour;
								$reponse=mysqli_query($this->baseDeDonnees->mysqli, "SELECT date FROM evenement");
								$i=0;
								while ($donnees=mysqli_fetch_array($reponse))
								{
									$dateBase=explode('-',$donnees['date']);
									$anneeBase=$dateBase[0];
									$moisBase=$dateBase[1];
									$jourBase=$dateBase[2];
									if ($anneeBase==$annee && $moisBase==$mois && $jourBase==$jour)
										$i=1;
								}
								$description=htmlentities($_POST['description'], ENT_QUOTES);
								if (isset($_POST['horaire']))
								{
									$horaire=$_POST['horaire'];
									if ($horaire=="on")
										$passage="00:00:01";
								}
								else
									$passage=$heure.':'.$minute.':00';
								$lieu=htmlentities($_POST['lieu'], ENT_QUOTES);
								$adresse=htmlentities($_POST['adresse'], ENT_QUOTES);
								$lien=htmlentities($_POST['lien'], ENT_QUOTES);
								if ($i==0)
								{
									mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO evenement VALUES('', '$nom', '$date', '$description', '$passage', '$lieu', '$adresse', '$lien', 'non')");
								}
							}
						}
					}
					break;
				
				case 'suppressionEvent':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id FROM evenement");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id=$donnees['id'];
							if (isset($_POST[$id]))
							{
								mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM evenement WHERE id = $id") OR DIE (mysqli_error());
							}
						}
					}
					break;
				
				case 'ajoutNews':
					if ($_SESSION['connect'])
					{
						if (isset($_POST['titre']))
						{
							$titre = htmlentities($_POST['titre'], ENT_QUOTES);
							mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO news VALUES('', '', '$titre', 'non', 'non')");
							$this->creation="La news '$titre' a bien �t� cr��e.";
						}
					}
					break;
				
				case 'ajoutElement':
					if ($_SESSION['connect'])
					{
						if (isset($_GET['news']))
						{
							$news=$_GET['news'];
							if (isset($_POST['paragraphe']))
							{
								if ($_POST['paragraphe']=="on")
								{
									mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO contenunews VALUES('', '', null, null, '$news')");
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
													mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO contenunews VALUES('', '', '$nom', null, '$news')");
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
									mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO contenunews VALUES('', '', null, '$nombreLigne', '$news')");
								}
							}
						}
					}
					break;
				
				case 'contenu':
					if ($_SESSION['connect'])
					{
						if (isset($_GET['news']))
						{
							$news=$_GET['news'];
							$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM contenunews WHERE pid=$news");
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
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE contenunews SET contenu = '$contenu' WHERE id = $id AND pid=$news") OR DIE (mysqli_error());
									}
								}
								else
								{
									if (isset($_POST[$id]))
									{
										$contenu=htmlentities($_POST[$id], ENT_QUOTES);
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE contenunews SET contenu = '$contenu' WHERE id = $id AND pid=$news") OR DIE (mysqli_error());
									}
								}
								if (isset($_POST['sup/'.$id]))
								{
									if($_POST['sup/'.$id]=='on')
									{
										mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM contenunews WHERE id = $id AND pid=$news") OR DIE (mysqli_error());
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
					}
					break;
				
				case 'validationNews':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, titre FROM news WHERE validation='non' ");
						$date = date("Y-m-d");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id = $donnees['id'];
							if (isset($_POST[$id]))
							{
								if ($_POST[$id]=='on')
									mysqli_query($this->baseDeDonnees->mysqli, "UPDATE news SET date = '$date', validation = 'oui' WHERE id = $id") OR DIE (mysqli_error());
							}
						}
					}
					break;
				
				case 'mailing':
					if ($_SESSION['connect'])
					{
						if(isset($_POST['nom']))
						{
							$nom = htmlentities($_POST['nom'], ENT_QUOTES);
							$mail = $_POST['mail'];
							mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO mailing VALUES('', '$nom', '$mail')");
						}
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id FROM mailing");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id=$donnees['id'];
							if (isset($_POST[$id]))
							{
								mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM mailing WHERE id = $id") OR DIE (mysqli_error());
							}
						}
					}
					break;
				
				case 'newsletter':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM utilisateurs");
						$donnees = mysqli_fetch_array($buff);
						$headers ="From: Tous unis contre le SIDA <postmaster@tousuniscontrelesida.fr>\r\n";
						$headers .="Reply-To: ".$donnees['mail']."\r\n";
						$headers .="Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
						$headers .="Content-Transfer-Encoding: 8bit";
						$objet = "Newsletter";
						$message = "";
						$j=1;
						$message .= "\n-- Les derni�res news post�es sur le site --\n\n";
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM news WHERE newsletter='non' AND validation='oui' ");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id=$donnees['id'];
							if (isset($_POST["news/".$id]))
							{
								switch ($_POST["news/".$id])
								{
									case 'envoi':
										$titre=html_entity_decode(stripslashes($donnees['titre']));
										$date=explode('-', $donnees['date']);
										$message .= "\n".$j.") ".$titre." (".$date[2]."/".$date[1]."/".$date[0].")\n";
										$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM contenunews WHERE pid=$id ORDER BY id");
										while($donnees2 = mysqli_fetch_array($buff2))
										{
											$texte = html_entity_decode(stripslashes($donnees2['contenu']));
											$texte = str_replace('[lien=', '', $texte);
											$texte = str_replace('[/lien]', '{%}', $texte);
											$texte = str_replace(']', '{%}', $texte);
											$texte = explode('{%}', $texte);
											$contenu = "";
											$k=0;
											for ($i=0; $i<count($texte); $i++)
											{
												if ($i==($k*2))
												{
													$contenu .= $texte[$i];
													$k++;
												}
												
											}
											if ($donnees2['nombreLigne']!=null)
											{
												$value = explode('|', $contenu);
												for ($i=0; $i<$donnees2['nombreLigne']; $i++)
												{
													if (isset($value[$i+1]))
														$message .= " - ".$value[$i+1]."\n";
												}
											}
											else
											{
												$message .= "\t".$contenu."\n";
											}
										}
										$j++;								
									case 'suppression':
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE news SET newsletter = 'oui' WHERE id = $id") OR DIE (mysqli_error());
										break;
									
									case 'rien':
									default:
										break;
								}
							}
						}
						$message .= "\n\n-- Les �v�nements r�cemment ajout�s --\n\n";
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM evenement WHERE newsletter='non' ");
						while($donnees = mysqli_fetch_array($buff))
						{
							$id=$donnees['id'];
							if (isset($_POST["event/".$id]))
							{
								switch ($_POST["event/".$id])
								{
									case 'envoi':
										$passageEvenement=$donnees['passage'];
										$date=$donnees['date'];
										$date=explode("-", $date);
										$annee=$date[0];
										$mois=$date[1];
										$jour=$date[2];
										$passageEvenement=explode(":", $passageEvenement);
										$heure=$passageEvenement[0];
										$minute=$passageEvenement[1];
										$seconde=$passageEvenement[2];
										
										$evenement = html_entity_decode(stripslashes($donnees['nom']));
										$message .= "\n".$j.") ".$evenement."\n";
										
										$message .= "date : ";
										$message .= $jour."/".$mois."/".$annee."\n";
										
										$descriptionEvenement = html_entity_decode(stripslashes($donnees['description']));
										$message .= "description : ";
										if ($descriptionEvenement!="")
											$message .= $descriptionEvenement."\n";
										else
											$message .= "non renseign�\n";
										
										$message .= "horaire de passage : ";
										if ($seconde==00)
											$message .= $heure."H".$minute."\n";
										else
											$message .= "non renseign�\n";
										
										$lieuEvenement = html_entity_decode(stripslashes($donnees['lieu']));
										$message .= "lieu : ";
										if ($lieuEvenement!="")
											$message .= $lieuEvenement."\n";
										else
											$message .= "non renseigne\n";
										
										$adresseEvenement = html_entity_decode(stripslashes($donnees['adresse']));
										$message .= "adresse : ";
										if ($adresseEvenement!="")
											$message .= $adresseEvenement."\n";
										else
											$message .= "non renseign�\n";
										
										$lienEvenement = html_entity_decode(stripslashes($donnees['lien']));
										$message .= "lien : ";
										if ($lienEvenement!="")
											$message .= $lienEvenement."\n";
										else
											$message .= "non renseigne\n";
										$j++;
									case 'suppression':
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE evenement SET newsletter = 'oui' WHERE id = $id") OR DIE (mysqli_error());
										break;
									
									case 'rien':
									default:
										break;
								}
							}
						} 
						if ($j>1)
						{
							$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM mailing");
							$donnees = mysqli_fetch_array($buff);
							if ($donnees[0]!=0)
							{
								$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT mail FROM mailing");
								$to = "";
								while($donnees = mysqli_fetch_array($buff))
								{
									$to .= $donnees['mail'].",";
								}
								$to .= "postmaster@tousuniscontrelesida.fr";
								$message = str_replace("&#039;", "'", $message);
								mail($to, $objet, $message, $headers);
							}
						}
					}
					break;
				
				case 'changeId':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM utilisateurs");
						$donnees = mysqli_fetch_array($buff);
						$id = $donnees['id'];
						if (isset($_POST['ancienId']) && isset($_POST['ancienPass']))
						{
							$ancienId=$_POST['ancienId'];
							$ancienPass=md5($_POST['ancienPass']);
							if ($ancienId==$donnees['login'] && $ancienPass==$donnees['password'] )
							{
								if (isset($_POST['nouvelId']) && isset($_POST['nouvelId2']))
								{
									if ($_POST['nouvelId']==$_POST['nouvelId2'] && $_POST['nouvelId']!="")
									{
										$nouvelId=$_POST['nouvelId'];
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE utilisateurs SET login = '$nouvelId' WHERE id = $id") OR DIE (mysqli_error());
										$this->login="Le changement d'identifiant � bien �t� effectu�";
									}
								}
								if (isset($_POST['nouveauPass']) && isset($_POST['nouveauPass2']))
								{
									if ($_POST['nouveauPass']==$_POST['nouveauPass2'] && $_POST['nouveauPass']!="")
									{
										$nouveauPass=md5($_POST['nouveauPass']);
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE utilisateurs SET password = '$nouveauPass' WHERE id = $id") OR DIE (mysqli_error());
										$this->password="Le changement de mot de passe � bien �t� effectu�";
									}
								}
								if (isset($_POST['nouveauMail']) && isset($_POST['nouveauMail2']))
								{
									if ($_POST['nouveauMail']==$_POST['nouveauMail2'] && $_POST['nouveauMail']!="")
									{
										$nouveauMail=$_POST['nouveauMail'];
										mysqli_query($this->baseDeDonnees->mysqli, "UPDATE utilisateurs SET mail = '$nouveauMail' WHERE id = $id") OR DIE (mysqli_error());
										$this->mail="Le changement de mail � bien �t� effectu�";
									}
								}
							}
						}
					}
					break;
				
				case 'accesRapide':
					if ($_SESSION['connect'])
					{
						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM modules WHERE type='call' AND module!='login' ");
						while ($donnees = mysqli_fetch_array($buff))
						{
							$id = $donnees['id'];
							if (isset($_POST["text/".$id]))
							{
								$module = $donnees['module'];
								if (!isset($_POST[$id]))
								{
									$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM accesrapide WHERE module='$module' ");
									$donnees2 = mysqli_fetch_array($buff2);
									if ($donnees2[0]!=0)
										mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM accesrapide WHERE module='$module' AND dir IS NULL ") OR DIE (mysqli_error());
								}
								else
								{
									if ($_POST[$id]=='on' && $_POST["text/".$id]!="" && $_POST["ordre/".$id]!="")
									{
										$ordre = (int)$_POST["ordre/".$id];
										if (is_integer($ordre))
										{
											$texte = htmlentities($_POST["text/".$id], ENT_QUOTES);
											$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM accesrapide WHERE module='$module' AND dir IS NULL ");
											$donnees2 = mysqli_fetch_array($buff2);
											if ($donnees2[0]==0)
											{
												mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO accesrapide VALUES('', '$texte', '$module', null, '$ordre')");
											}
											else
												mysqli_query($this->baseDeDonnees->mysqli, "UPDATE accesrapide SET texte = '$texte', ordre = '$ordre' WHERE module='$module' AND dir IS NULL ") OR DIE (mysqli_error());
										}
									}
								}
							}
							if (!isset($_POST[$id]))
							{
								
							}
							if ($donnees['module']=="galerie")
							{
								$path="galerie";
								$dossier=opendir($path);
								$k=0;
								$dir=array("");
								while ($file=readdir($dossier))
								{
									if ($file!="." && $file!="..")
									{
										if (is_dir($path."/".$file))
										{
											$dir[$k]=$file;
											$k++;
										}
									}
								}
								closedir($dossier);
								
								if ($k!=0)
								{
									for ($i=0; $i<$k; $i++)
									{
										if (isset($_POST["text/".$dir[$i]]))
										{
											$module = $donnees['module'];
											$directory=$dir[$i];
											if (!isset($_POST[$dir[$i]]))
											{
												$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM accesrapide WHERE module='$module' AND dir='$directory' ");
												$donnees2 = mysqli_fetch_array($buff2);
												if ($donnees2[0]!=0)
													mysqli_query($this->baseDeDonnees->mysqli, "DELETE FROM accesrapide WHERE module='$module' AND dir='$directory' ") OR DIE (mysqli_error());
											}
											else
											{
												if ($_POST[$dir[$i]]=='on' && $_POST["text/".$dir[$i]]!="" && $_POST["ordre/".$dir[$i]]!="")
												{
													$ordre = (int)$_POST["ordre/".$dir[$i]];
													if (is_integer($ordre))
													{
														$texte = htmlentities($_POST["text/".$dir[$i]], ENT_QUOTES);
														$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT count(*) FROM accesrapide WHERE module='$module' AND dir='$directory' ");
														$donnees2 = mysqli_fetch_array($buff2);
														if ($donnees2[0]==0)
															mysqli_query($this->baseDeDonnees->mysqli, "INSERT INTO accesrapide VALUES('', '$texte', '$module', '$directory', '$ordre')");
														else
															mysqli_query($this->baseDeDonnees->mysqli, "UPDATE accesrapide SET texte = '$texte', ordre = '$ordre' WHERE module='$module' AND dir='$directory' ") OR DIE (mysqli_error());
													}
												}
											}
										}
									}
								}
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
			echo "<div id=\"login\" >";
			
			switch($_GET['action'])
			{
				case 'connexion':
				case 'login':
?>
		<form method="post" action="?module=login&action=connexion">
			<fieldset>
				<legend>Administration</legend>
				<p>
					identifiez-vous :<br/>
					<input type="text" name="login" value="identifiant" class="inputText" />
				</p>
				<p>
					mot de passe : <br/>
					<input type="password" name="password" class="inputText" /><br/>
				</p>
				<p>
					<input  type="submit" />
					<input  type="reset" />
				</p>
			</fieldset>
		</form>
<?php
					break;
				
				case 'ajoutDossier':
				case 'ajoutPhotos':
					if ($_SESSION['connect'])
					{
						$path="galerie";
						$dossier=opendir($path);
						$dir[0]="";
						$j=0;
						while ($file=readdir($dossier))
						{
							if ($file!="." && $file!="..")
							{
								if (is_dir($path."/".$file))
								{
									$dir[$j]=$file;
									$j++;
								}
							}
						}
						closedir($dossier);
						
						if ($dir[0]!="")
						{

?>
							<form method="post" action="?module=login&action=ajoutPhotos" enctype="multipart/form-data">
								<fieldset>
									<legend>Ajouter une photo</legend>
									<p>
										<label for="photo"><strong>S�lectionner la photos � inclure dans la galerie : </strong></label><br/>
										<input type="file" name="photo"/>
									</p>
									<p>
										<label for="photo"><strong>S�lectionner la galerie correspondante : </strong></label><br/>
										<select name="dir">
<?php

							for ($i=0; $i<count($dir); $i++)
							{
								echo "\n<option value=\"".$dir[$i]."\">".$dir[$i]."</option>";
							}

?>
										</select>
									</p>
									<p>
										<input type="submit" />
										<input type="reset" /><br/>
										<?php echo "<span class=\"error\" >".$this->erreur."</span>"; ?>
									</p>
								</fieldset>
							</form>
<?php

						}
						else
						{
							echo "<span class=\"error\" >il faut cr�er un dossier galerie avant afin d'ins�rer les photos dedans.</span>";
						}
						
						

?>
							<form method="post" action="?module=login&action=ajoutDossier">
								<fieldset>
									<legend>Cr�er un dossier galerie</legend>
									<p>
										<label for="photo"><strong>indiquer le nom du dossier � cr�er : </strong></label><br/>
										<input type="text" name="dir" class="inputText" />
									</p>
									<p>
										<input type="submit" />
										<input type ="reset" />
									</p>
								</fieldset>
							</form>
							
							<form method="post" action="?module=login&action=supressionDossier">
								<fieldset>
									<legend>Supprimer un dossier galerie</legend>
									<p>
<?php

							for ($i=0; $i<count($dir); $i++)
							{
								echo "\n<input type=\"checkbox\" name=\"".$dir[$i]."\" />".$dir[$i]."<br/>";
							}

?>
									</p>
									<p>
										<input type="submit" />
										<input type ="reset" />
									</p>
								</fieldset>
							</form>
<?php

					}
					break;
					
				case 'ajoutEvent':
					if ($_SESSION['connect'])
					{

?>
				<form method="post" action="?module=login&action=ajoutEvent">
					<fieldset>
						<legend>Ajouter un �v�nement</legend>
						<p>
							<label for="nom"><strong>nom (*) :
<?php

						if(isset($_POST['nom']))
						{
							if ($_POST['nom']=="")
								echo '<span class="erreur">Ce champ doit �tre renseign�</span>';
						}

?>
							</strong></label><br/>
							<input type="text" name="nom" class="inputText" value="<?php	if(isset($_POST['nom']))	echo $_POST['nom'];	?>"/>
						</p>
						<p>
							<label for="description"><strong>description : </strong></label><br/>
							<textarea name="description" ><?php	if(isset($_POST['description']))	echo $_POST['description'];	?></textarea>
						</p>
						<p>
							<strong>date (*) : </strong><?php if (isset($i)) {if ($i==1) echo '<span class="erreur">Un �v�nement �xiste d�j� � cette date</span>';} ?><br/>
<?php

						$jour=1;
						$mois=1;
						$an=date("Y");
						$annee=$an;
						echo "<label for=\"jour\">jour : </label>";
						echo "\n<select name=\"jour\">";
						for ($i=1;$i<=31;$i++)
						{
							if ($jour==$i)
								echo "\n\t<option selected=\"selected\" value=\"".$i."\">".$i."</option>";
							else
								echo "\n\t<option value=\"".$i."\">".$i."</option>";
						}
						echo "\n</select>";
						
						$tableauMois=array("janvier","f�vrier","mars","avril","mai","juin","juillet","ao�t","septembre","octobre","novembre","d�cembre");
						echo "\n<label for=\"mois\"> mois : </label>";
						echo "\n<select name=\"mois\">";
						for ($i=1;$i<=12;$i++)
						{
							if ($mois==$i)
								echo "\n\t<option selected=\"selected\" value=\"".$i."\">".$tableauMois[$i-1]."</option>";
							else
								echo "\n\t<option value=\"".$i."\">".$tableauMois[$i-1]."</option>";
						}
						echo "\n</select>";
						
						echo "\n<label for=\"annee\"> annee : </label>";
						echo "\n<select name=\"annee\">";
						for ($i=0;$i<=2;$i++)
						{
							if ($annee==($an+$i))
								echo "\n\t<option selected=\"selected\" value=\"".($an+$i)."\">".($an+$i)."</option>";
							else
								echo "\n\t<option value=\"".($an+$i)."\">".($an+$i)."</option>";
						}
						echo "\n</select>\n"

?>
						</p>
						<p>
							<strong>horaire de passage (**) : </strong><br/>
<?php

						$heure=0;
						$minute=0;
						echo "<label for=\"heure\">heure : </label>";
						echo "\n<select name=\"heure\">";
						for ($i=0;$i<=23;$i++)
						{
							if ($heure==$i)
								echo "\n\t<option selected=\"selected\" value=\"".$i."\">".$i."</option>";
							else
								echo "\n\t<option value=\"".$i."\">".$i."</option>";
						}
						echo "\n</select>";
						
						echo "\n<label for=\"minute\"> minute : </label>";
						echo "\n<select name=\"minute\">";
						for ($i=0;$i<=59;$i++)
						{
							if ($minute==$i)
								echo "\n\t<option selected=\"selected\" value=\"".$i."\">".$i."</option>";
							else
								echo "\n\t<option value=\"".$i."\">".$i."</option>";
						}
						echo "\n</select>\n";

?>
						</p>
						<p>
							<input type="checkbox" name="horaire"/><label for="horaire">Ne pas renseigner l'horaire de passage</label>
						</p>
						<p>
							<label for="lieu"><strong>lieu : </strong></label><br/>
							<input type="text" name="lieu" class="inputText" value="<?php	if(isset($_POST['lieu']))	echo $_POST['lieu'];	?>"/>
						</p>
						<p>
							<label for="adresse"><strong>adresse : </strong></label><br/>
							<textarea name="adresse" ><?php	if(isset($_POST['adresse']))	echo $_POST['adresse'];	?></textarea>
						</p>
						<p>
							<label for="lien"><strong>lien : </strong></label><br/>
							<input type="text" name="lien" class="inputText" value="<?php	if(isset($_POST['lien']))	echo $_POST['lien'];	?>"/>
						</p>
						<p>
							<input type="submit" />
							<input type="reset" />
						</p>
						<p class="petit">
							Les champs suivis de (*) doivent obligatoirement �tre renseign�s.<br/>
							/!\ En cas d'oubli, la date est automatiquement fix� par d�fault au 1er janvier de l'ann�e en cours.<br/>
							Le champ suivi de (**) peut ne pas �tre renseign� en cochant la case "Ne pas renseigner l'horaire de passage".<br/>
							/!\ En cas d'oubli, l'heure de passage est automatiquement fix� par d�fault � 00H00.
						</p>
					</fieldset>
				</form>
<?php

					}
					break;
				
				case 'suppressionEvent':
					if ($_SESSION['connect'])
					{

?>
						<form method="post" action="?module=login&action=suppressionEvent">
							<fieldset>
								<legend>Supprimer un �v�nement</legend>
								<ul>
<?php

						$date = date("Y-m-d");
						$reponse=mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM evenement WHERE date >= '$date'");
						while ($donnees=mysqli_fetch_array($reponse))
						{
							$date = explode('-', $donnees['date']);
							echo "<li><input type=\"checkbox\" name=\"".$donnees['id']."\" /><label for=\"".$donnees['id']."\" > ".$donnees['nom']." (".$date[2]."/".$date[1]."/".$date[0].")</label></li>";
						}

?>
								</ul>
								<p>
									<input type="submit" />
									<input type ="reset" /><br/>
								</p>
							</fieldset>
						</form>
<?php

					}
					break;
				
				case 'ajoutNews':
					if ($_SESSION['connect'])
					{

?>
						<form method="post" action="?module=login&action=ajoutNews">
							<fieldset>
								<legend>Cr�er une nouvelle news</legend>
								<p>
									<label for="titre" >Choisissez le titre de la news : </label><br/>
									<input type="text" name="titre" class="inputText" />
								</p>
								<p>
									<input type="submit" />
									<input type ="reset" /><br/>
									<?php echo "<span class=\"error\" >".$this->creation."</span>"; ?>
								</p>
							</fieldset>
						</form>
<?php

					}
					break;
				
				case 'ajoutElement':
				case 'contenu':
				case 'modificationNews':
					if ($_SESSION['connect'])
					{
						if (isset($_GET['news']))
						{
							$news = $_GET['news'];

?>
							<form method="post" action="?module=login&action=ajoutElement&news=<?php echo $news; ?>" enctype="multipart/form-data" >
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
									<p>
										<input type="submit" />
										<input type ="reset" />
									</p>
								</fieldset>
							</form>
							<p>
								Pour ajouter un lien, suivez le mod�le suivant : [lien=l_adresse_du_lien]texte_personnalis�[/lien].
							</p>
							<ul>
								<li>Par exemple : Voici le lien vers [lien=http://www.tousuniscontrelesida.fr]notre site[/lien].</li>
								<li>Donnera r�ellement : Voici le lien vers <a href="http://www.tousuniscontrelesida.fr">notre site</a>.</li>
							</ul>
							<form method="post" action="?module=login&action=contenu&news=<?php echo $news; ?>" >
								<fieldset>
									<legend>Modifier le contenu</legend>
									<div id="elements" >
<?php

							$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM contenunews WHERE pid=$news ORDER BY id");
							while($donnees = mysqli_fetch_array($buff))
							{
								if ($donnees['image']!=null)
								{
									echo "paragraphe avec image image : <img src=\"image/".htmlentities($donnees['image']).".jpg\" alt=\"".$donnees['image']."\" width=\"100\" />"; 
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
									echo "paragraphe simple :";
									echo "<textarea name=\"".$donnees['id']."\" >".$donnees['contenu']."</textarea><br/>";
									echo "<input type=\"checkbox\" name=\"sup/".$donnees['id']."\" /> supprimer";
								}
								echo "<hr/>";
							}

?>
										</div>
										<p>
											<input type="submit"/ >
											<input type ="reset" />
										</p>
								</fieldset>
							</form>
<?php

						}
						else
						{
							echo "<h3>Liste des news non valid�es</h3>";
							echo "<ul>";
							$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, titre FROM news WHERE validation='non' ");
							while($donnees = mysqli_fetch_array($buff))
							{
								echo "<li><a href=\"?module=login&action=modificationNews&news=".$donnees['id']."\">".$donnees['titre']."</a></li>";
							}
							echo "</ul>";
						}
					}
					break;
				
				case 'validationNews':
					if ($_SESSION['connect'])
					{

?>
				<form method="post" action="?module=login&action=validationNews">
					<fieldset>
						<legend>Valider les news</legend>
						<ul>
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, titre FROM news WHERE validation='non' ");
						while($donnees = mysqli_fetch_array($buff))
						{
							echo "<li><input type=\"checkbox\" name=\"".$donnees['id']."\" /><label for=\"".$donnees['id']."\" > ".$donnees['titre']."</label></li>";
						}

?>
						</ul>
						<p>
							<input type="submit" />
							<input type ="reset" />
						</p>
					</fieldset>
				</form>
<?php

					}
					break;
					
				case 'mailing':
					if ($_SESSION['connect'])
					{

?>
				<form method="post" action="?module=login&action=mailing">
					<fieldset>
						<legend>Ajouter un compte sur la mailing list</legend>
						<p>
							<label for="nom" >Nom : </label><input type="text" name="nom" class="inputText" />
						</p>
						<p>
							<label for="nom" >Mail&nbsp; : </label><input type="text" name="mail" class="inputText" />
						</p>
						<p>
							<input type="submit" />
							<input type ="reset" />
						</p>
					</fieldset>
				</form>
				<form method="post" action="?module=login&action=mailing">
					<fieldset>
						<legend>Supprimer les comptes de la mailing list</legend>
						<ul>
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM mailing");
						while($donnees = mysqli_fetch_array($buff))
						{
							echo "<li><input type=\"checkbox\" name=\"".$donnees['id']."\" /><label for=\"".$donnees['id']."\" > ".$donnees['nom']." (".$donnees['mail'].")</label></li>";
						}

?>
						</ul>
						<p>
							<input type="submit" />
							<input type ="reset" />
						</p>
					</fieldset>
				</form>
<?php

					}
					break;
				
				case 'newsletter':
					if ($_SESSION['connect'])
					{

?>
				<form method="post" action="?module=login&action=newsletter">
					<fieldset>
						<legend>Envoyer la newsletter</legend>
						<table>
							<thead>
								<tr>
									<td class="firstColumn" >News non encore envoy�es</td>
									<td>Envoyer</td>
									<td>Ne pas envoyer</td>
									<td>Ne rien faire</td>
								</tr>
							</thead>
							<tbody>
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, titre FROM news WHERE newsletter='non' AND validation='oui' ");
						while($donnees = mysqli_fetch_array($buff))
						{
							echo "<tr>";
							echo "<td class=\"firstColumn\" >".$donnees['titre']."</td>";
							echo "<td><input type=\"radio\" name=\"news/".$donnees['id']."\" value=\"envoi\" /></td>";
							echo "<td><input type=\"radio\" name=\"news/".$donnees['id']."\" value=\"suppression\" /></td>";
							echo "<td><input type=\"radio\" name=\"news/".$donnees['id']."\" value=\"rien\" /></td>";
							echo "</tr>";
						}

?>
							</tbody>
						</table>
						<table>
							<thead>
								<tr>
									<td class="firstColumn" >Ev�nements non encore envoy�s</td>
									<td>Envoyer</td>
									<td>Ne pas envoyer</td>
									<td>Ne rien faire</td>
								</tr>
							</thead>
							<tbody>
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT id, nom FROM evenement WHERE newsletter='non' ");
						while($donnees = mysqli_fetch_array($buff))
						{
							echo "<tr>";
							echo "<td class=\"firstColumn\" >".$donnees['nom']."</td>";
							echo "<td><input type=\"radio\" name=\"event/".$donnees['id']."\" value=\"envoi\" /></td>";
							echo "<td><input type=\"radio\" name=\"event/".$donnees['id']."\" value=\"suppression\" /></td>";
							echo "<td><input type=\"radio\" name=\"event/".$donnees['id']."\" value=\"rien\" /></td>";
							echo "</tr>";
						}
?>
							</tbody>
						</table>
						<p>
							/!\ Si vous choississez "Ne pas envoyer", il ne vous sera plus demander si vous voulez envoyer cette nouveaut�.
						</p>
						<p>
							<input type="submit" value="Envoyer la newsletter" />
							<input type="reset" />
						</p>
					</fieldset>
				</form>
<?php
					}
					break;
				
				case 'changeId':
					if ($_SESSION['connect'])
					{
?>
				<form method="post" action="?module=login&action=changeId">
					<fieldset>
						<legend>Rappel de l'identifiant et mot de passe actuel</legend>
						<p>
							Doit �tre rempli pour pouvoir proc�der au changement d'une quelconque donn�e.
						</p>
						<p>
							<label for="ancienId"><strong>Identifiant de connexion :</strong></label><br/>
							<input type="text" name="ancienId" class="inputText" />
						</p>
						<p>
							<label for="ancienPass"><strong>Mot de passe :</strong></label><br/>
							<input type="password" name="ancienPass" class="inputText" />
						</p>
					</fieldset>
					<fieldset>
						<legend>Changer l'identifiant</legend>
						<p>
							<label for="nouvelId"><strong>Nouvel identifiant de connexion (*2) :</strong></label><br/>
							<input type="text" name="nouvelId" class="inputText" /><br/>
							<input type="text" name="nouvelId2" class="inputText" /><br/>
							<span class="error"><?php echo $this->login; ?></span>
						</p>
					</fieldset>
					<fieldset>
						<legend>Changer le mot de passe</legend>
						<p>
							<label for="nouveauPass"><strong>Nouveau mot de passe (*2) :</strong></label><br/>
							<input type="password" name="nouveauPass" class="inputText" /><br/>
							<input type="password" name="nouveauPass2" class="inputText" /><br/>
							<span class="error"><?php echo $this->password; ?></span>
						</p>
					</fieldset>
					<fieldset>
						<legend>Changer l'adresse de messagerie de l'association</legend>
						<p>
							Actuellement : 
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT mail FROM utilisateurs");
						$donnees = mysqli_fetch_array($buff);
						echo $donnees['mail'];

?>
						</p>
						<p>
							<label for="id"><strong>Nouveau mail (*2) :</strong></label><br/>
							<input type="text" name="nouveauMail" class="inputText" /><br/>
							<input type="text" name="nouveauMail2" class="inputText" /><br/>
							<span class="error"><?php echo $this->mail; ?></span>
						</p>
					</fieldset>
						<p>
							<input type="submit" />
							<input type="reset" />
						</p>
				</form>
<?php
					}
					break;
				
				case 'accesRapide':
					if ($_SESSION['connect'])
					{
?>
				<form method="post" action="?module=login&action=accesRapide">
					<fieldset>
						<legend>G�rer le menu "Acc�s rapide"</legend>
						<p>
							Cochez/d�cocher les modules dont vous voulez qu'ils apparaissent dans le menu acc�s rapide � droite et indiquez le texte qui va appara�tre ainsi que l'ordre dans le menu.
						</p>
						<ul>
<?php

						$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM modules WHERE type='call' AND module!='login' ");
						while ($donnees = mysqli_fetch_array($buff))
						{
							$existe=false;
							$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM accesrapide ");
							while ($donnees2 = mysqli_fetch_array($buff2))
							{
								if ($donnees2['module']==$donnees['module'] && $donnees2['dir']==null)
								{
									$existe=true;
									break;
								}
							}
							
							echo '<li>';
							if ($existe)
							{
								echo '<input type="checkbox" name="'.$donnees['id'].'" checked="checked" /> '.$donnees['nom'].'<br/>';
								echo '<div>';
								echo 'texte : <input type="text" name="text/'.$donnees['id'].'" value="'.$donnees2['texte'].'" />';
								echo 'ordre : <input type="text" name="ordre/'.$donnees['id'].'" style="width: 30px;" value="'.$donnees2['ordre'].'" />';
								}
							else
							{
								echo '<input type="checkbox" name="'.$donnees['id'].'" /> '.$donnees['nom'].'<br/>';
								echo '<div>';
								echo 'texte : <input type="text" name="text/'.$donnees['id'].'" />';
								echo 'ordre : <input type="text" name="ordre/'.$donnees['id'].'" style="width: 30px;" />';
							}
							echo '</div>';
							if ($donnees['module']=="galerie")
							{
								$path="galerie";
								$dossier=opendir($path);
								$k=0;
								$dir=array("");
								while ($file=readdir($dossier))
								{
									if ($file!="." && $file!="..")
									{
										if (is_dir($path."/".$file))
										{
											$dir[$k]=$file;
											$k++;
										}
									}
								}
								closedir($dossier);
								
								if ($k!=0)
								{
									echo "<ul>";
									for ($i=0; $i<$k; $i++)
									{
										$existe=false;
										$buff2 = mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM accesrapide ");
										while ($donnees2 = mysqli_fetch_array($buff2))
										{
											if ($donnees2['module']==$donnees['module'] && $donnees2['dir']==$dir[$i])
											{
												$existe=true;
												break;
											}
										}
										
										echo '<hr class="clear" />';
										echo '<li>';
										if ($existe)
										{
											echo '<input type="checkbox" name="'.$dir[$i].'" checked="checked" /> le dossier galerie "'.$dir[$i].'"';
											echo '<div style="margin-left:-40px;" >';
											echo 'texte : <input type="text" name="text/'.$dir[$i].'" value="'.$donnees2['texte'].'" />';
											echo 'ordre : <input type="text" name="ordre/'.$dir[$i].'" style="width: 30px;" value="'.$donnees2['ordre'].'" />';
										}
										else
										{
											echo '<input type="checkbox" name="'.$dir[$i].'" /> le dossier galerie "'.$dir[$i].'"';
											echo '<div style="margin-left:-40px;" >';
											echo 'texte : <input type="text" name="text/'.$dir[$i].'"/>';
											echo 'ordre : <input type="text" name="ordre/'.$dir[$i].'" style="width: 30px;" />';
										}
										echo '</div>';
										echo '</li>';
									}
									echo "</ul>";
								}
							}
							echo "</li>";
							echo "<hr class=\"clear\" />";
						}

?>
						<ul/>
						<p>
							<input type="submit" />
							<input type="reset" />
						</p>
					</fieldset>
				</form>
<?php
					}
					break;
				
				default:
					break;
			}
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {

?>
		<div class="bandeau" >
			<a href="?module=login&action=login" >Administration</a>
		</div>
		<div id="administration" >
<?php

			if ($_SESSION['connect'])
			{

?>
			<ul>
				<li><a href="?module=login&action=ajoutPhotos">Ajouter dans la galerie</a></li>
				<li><a href="?module=login&action=ajoutEvent">Ajouter un �v�nement</a></li>
				<li><a href="?module=login&action=suppressionEvent">Supprimer un �v�nement</a></li>
				<li><a href="?module=login&action=ajoutNews">Ajouter une news</a></li>
				<li><a href="?module=login&action=modificationNews">Modifier une news</a></li>
				<li><a href="?module=login&action=validationNews">Valider une news</a></li>
				<li><a href="?module=login&action=mailing">G�rer la mailing list</a></li>
				<li><a href="?module=login&action=newsletter">Envoyer la newsletter</a></li>
				<li><a href="?module=login&action=changeId">G�rer le profil de connexion</a></li>
				<li><a href="?module=login&action=accesRapide">G�rer le menu "Acc�s rapide"</a></li>
			</ul>
		<form method="post" action="?module=login&action=deconnexion">
			<p>
				<input type="checkbox" name="deconnexion" /><label for="deconnexion"> d�connexion</label>
				<input type="submit"/>
			</p>
		</form>
<?php

			}
			echo "</div>";
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "Administration > ";
			switch($_GET['action'])
			{
				case 'ajoutDossier':
				case 'ajoutPhotos':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Ajouter dans la galerie</a>";
					break;
					
				case 'ajoutEvent':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Ajouter un �v�nement</a>";
					break;
				
				case 'SuppressionEvent':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Supprimer un �v�nement</a>";
					break;
				
				case 'ajoutNews':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Ajouter un news</a>";
					break;
				
				case 'ajoutElement':
				case 'contenu':
				case 'modificationNews':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Modifier une news</a>";
					break;
				
				case 'validationNews':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Valider une news</a>";
					break;
					
				case 'mailing':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">G�rer la mailing list</a>";
					break;
				
				case 'newsletter':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">Envoyer la newsletter</a>";
					break;
				
				case 'changeId':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">G�rer le profil de connexion</a>";
					break;
				
				case 'accesRapide':
					echo "<a href=\"?module=login&action=".$_GET['action']."\">G�rer le menu \"Acc�s rapide\"</a>";
					break;
				
				default:
					echo "<a href=\"?module=login&action=".$_GET['action']."\">".$_GET['action']."</a>";
					break;
			}
			if (isset($_GET['news']))
			{
				$news=$_GET['news'];
				echo " > ";
				$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT titre FROM news WHERE id=$news ");
				$donnees = mysqli_fetch_array($buff);
				echo "<a href=\"?module=login&action=".$_GET['action']."&news=".$news."\">".$donnees['titre']."</a>";
			}
			echo "</p>";
			echo "</div>";
		}
		
		
	}

?>