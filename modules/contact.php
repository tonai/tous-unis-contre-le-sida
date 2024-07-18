<?php

	class Contact extends ModuleAbstrait {
		
		var $erreurNom="";
		var $erreurPrenom="";
		var $erreurMail="";
		var $erreurObjet="";
		var $erreurMessage="";
		var $enregistrement="";

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


		
		function Plan() {
		}
		
		
		
		function preTraitement($action) {
			switch($action)
			{
				case 'send':
					if (isset($_POST['nom']))
					{
						$this->erreurNom="";
						$this->erreurPrenom="";
						$this->erreurMail="";
						$this->erreurObjet="";
						$this->erreurMessage="";
						$this->enregistrement="";
						
						if ($_POST['nom']!='' && $_POST['prenom']!='' && $_POST['mail']!='' && $_POST['objet']!='' && $_POST['message']!='')
						{
							$nom=htmlspecialchars($_POST['nom'], ENT_QUOTES);
							$prenom=htmlspecialchars($_POST['prenom'], ENT_QUOTES);
							$mail=htmlspecialchars($_POST['mail'], ENT_QUOTES);
							$objet=htmlspecialchars($_POST['objet'], ENT_QUOTES);
							$message=htmlspecialchars($_POST['message'], ENT_QUOTES);
							$message = wordwrap($message, 70);
							
							//$date=date("Y-m-d H:i:s");
							
							$headers ="From: $nom $prenom <$mail>\r\n";
							$headers .="Reply-To: $mail\r\n";
							$headers .="Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
							$headers .="Content-Transfer-Encoding: 8bit"; 
							/*
							$messageTexte="Bonjour,\n\nVoici un message au format texte"; 
							$messageHTML="<html>
							<head>
							<title>Titre</title>
							</head>
							<body>Test de message</body>
							</html>";
							
							$frontiere = "-----=".md5(uniqid(mt_rand()));
							
							$message = "This is a multi-part message in MIME format.\n\n";
							$message .= "--".$frontiere."--\n";
							$message .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
							$message .= "Content-Transfer-Encoding: 8bit\n\n";
							$message .= $messageTexte."\n\n";
							$message .= "--".$frontiere."--\n";
							$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
							$message .= "Content-Transfer-Encoding: 8bit\n\n";
							$message .= $messageHTML."\n\n";
							$message .= "--".$frontiere."--\n"; 
							*/
							$buff = mysqli_query($this->baseDeDonnees->mysqli, "SELECT mail FROM utilisateurs");
							$donnees = mysqli_fetch_array($buff);
							mail($donnees['mail'], $objet, $message, $headers);
							
							$this->enregistrement="Votre message � bien �t� envoy�.";
						}
						if ($_POST['nom']=='')
						{
							$this->erreurNom="Aucun nom n'a �t� renseign�.";
						}
						if ($_POST['prenom']=='')
						{
							$this->erreurPrenom="Aucun pr�nom n'a �t� renseign�.";
						}
						if ($_POST['mail']=='')
						{
							$this->erreurMail="Aucun mail n'a �t� renseign�.";
						}
						if ($_POST['objet']=='')
						{
							$this->erreurObjet="Aucun objet n'a �t� renseign�.";
						}
						if ($_POST['message']=='')
						{
							$this->erreurMessage="Aucun message n'a �t� renseign�.";
						}
					}
					break;
				
				default:
					break;
			}
		}
		
		
		
		function afficherPage() {
			$this->navigation();

?>
			<div id="contact">
				<p>
					Veuillez remplir tous les champs pour envoyer un message � l'association.
				</p>
				<form method="post" action="?module=contact&action=send" >
					<table>
						<tr>
							<td><label for="nom" >Nom : </label></td>
							<td><input type="text" name="nom" value="<?php if(isset($_POST['nom']) && $this->erreurNom=="") echo $_POST['nom']; ?>" /></td>
							<td><span class="error" ><?php echo $this->erreurNom; ?></span></td>
						</tr>
						<tr>
							<td><label for="prenom" >Pr�nom : </label></td>
							<td><input type="text" name="prenom" value="<?php if(isset($_POST['prenom']) && $this->erreurPrenom=="") echo $_POST['prenom']; ?>" /></td>
							<td><span class="error" ><?php echo $this->erreurPrenom; ?></span></td>
						</tr>
						<tr>
							<td><label for="mail" >E-mail : </label></td>
							<td><input type="text" name="mail" value="<?php if(isset($_POST['mail']) && $this->erreurMail=="") echo $_POST['mail']; ?>" /></td>
							<td><span class="error" ><?php echo $this->erreurMail; ?></span></td>
						</tr>
						<tr>
							<td><label for="mail" >Objet : </label></td>
							<td><input type="text" name="objet" value="<?php if(isset($_POST['objet']) && $this->erreurObjet=="") echo $_POST['objet']; ?>" /></td>
							<td><span class="error" ><?php echo $this->erreurObjet; ?></span></td>
						</tr>
						<tr>
							<td><label for="message" > Message : </label></td>
							<td></span></td>
							<td><span class="error" ><?php echo $this->erreurMessage; ?></span></td>
						</tr>
						<tr>
							<td colspan="3" >
								<textarea name="message" ><?php if(isset($_POST['message']) && $this->erreurMessage=="") echo $_POST['message']; ?></textarea>
							</td>
						</tr>
						<tr>
							<td><input type="submit" value="envoyer le message" /></td>
							<td><input type="reset" /></td>
						</tr>
					</table>
					<p><span class="error" ><?php echo $this->enregistrement; ?></span></p>
				</form>
			</div>
<?php

		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=contact\" >Contact</a>";
			echo "</p>";
			echo "</div>";
		}
		
	}