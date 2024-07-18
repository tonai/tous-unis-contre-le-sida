<?php

	class Evenements extends ModuleAbstrait {
	
		var $tableauMois=array("janvier","f�vrier","mars","avril","mai","juin","juillet","ao�t","septembre","octobre","novembre","d�cembre");
		var $tableauJour=array("lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");

    function __construct($baseDeDonnees) {
      $this->baseDeDonnees = $baseDeDonnees;
    }


	
		function Evenements() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
			$this->navigation();

?>
			<div id="evenements">
				<table>

<?php

			/* initialisation des variables annees, mois correpondant */
			$annee=date("Y");
			if (isset($_GET['mois']))
			{
				$moisActuel=$_GET['mois'];
				if ($moisActuel<date("m"))
					$annee++;
			}
			else
			{
				$moisActuel=date("m");
			}
			$timestamp=mktime(0,0,0,$moisActuel,1,$annee);
			$premierJour=date("w",$timestamp);
			if ($premierJour==0)
				$premierJour=7;
			$bissextile=date("L");
			
			/* Calcul du Lundi de P�ques, Jeudi de l'Ascension et Lundi de Pentec�te */
			$n=(int)$annee-1900;
			$a=$this->divisionEuclidienne($n, 19, 1);
			$x=$a*7+1;
			$b=$this->divisionEuclidienne($x, 19, 0);
			$y=(11*$a)-$b+4;
			$c=$this->divisionEuclidienne($y, 29, 1);
			$d=$this->divisionEuclidienne($n, 4, 0);
			$z=$n-$c+$d+31;
			$e=$this->divisionEuclidienne($z, 7, 1);
			$paques=25-$c-$e+1;
			$ascension=$paques+38;
			$pentecote=$paques+49;
			if ($paques<=0)
			{
				$jourPaques=31+$paques;
				$moisPaques=3;
			}
			else
			{
				$jourPaques=$paques;
				$moisPaques=4;
			}
			if ($ascension<=30)
			{
				$jourAscension=$ascension;
				$moisAscension=4;
			}
			else
			{
				$jourAscension=$ascension-30;
				$moisAscension=5;
			}
			if ($pentecote<=61)
			{
				$jourPentecote=$pentecote-30;
				$moisPentecote=5;
			}
			else
			{
				$jourPentecote=$pentecote-61;
				$moisPentecote=6;
			}
			
			/* on r�cup�re les �v�nements du mois dans la base  */
			$date1=$annee."-".$moisActuel."-01";
			$date2=$annee."-".$moisActuel."-31";
			$reponse=mysqli_query($this->baseDeDonnees->mysqli, "SELECT * FROM evenement WHERE date >= '$date1' AND date <='$date2'");
			$nbEvenement=0;
			$jourEvenement=array("");
			$evenement=array("");
			while ($donnees=mysqli_fetch_array($reponse))
			{
				$dateEvenement=explode("-",$donnees['date']);
				$jourEvenement[$nbEvenement]=$dateEvenement[2];
				$evenement[$nbEvenement]=$donnees['nom'];
				$nbEvenement++;
			}
			
			/* on affiche le tableau */
			if ($moisActuel==4 || $moisActuel==6 || $moisActuel==9 || $moisActuel==11)
			{
				$jourMax=30;
			}
			elseif ($moisActuel==2)
			{
				if ($bissextile)
					$jourMax=29;
				else
					$jourMax=28;
			}
			else
			{
				$jourMax=31;
			}
			if (($moisActuel)==1)
				$moisPrecedant=12;
			else
				$moisPrecedant=($moisActuel-1);
			if (($moisActuel)==12)
				$moisSuivant=1;
			else
				$moisSuivant=($moisActuel+1);
			echo "<caption>";
			if ($moisActuel!=date("m"))
				echo '<a href="?module=evenements&mois='.$moisPrecedant.'" class="left" ><<</a>';
			if ($moisActuel!=(date("m")-1))
				echo '<a href="?module=evenements&mois='.$moisSuivant.'" class="right" >>></a>';
			echo $this->tableauMois[$moisActuel-1].' '.$annee;
			echo "</caption>";
			echo "\n<thead>";
			echo "\n\t<tr>";
			for ($i=0;$i<7;$i++)
			{
				if ($i==5 || $i==6)
					echo "\n\t\t<td class=\"ferie\">".$this->tableauJour[$i]."</td>";
				else
					echo "\n\t\t<td>".$this->tableauJour[$i]."</td>";
			}
			echo "\n\t</tr>";
			echo "\n</thead>";
			echo "\n<tbody>";
			$jour=1;
			$j=1;
			while ($jour<=$jourMax)
			{
				echo "\n\t<tr>";
				for ($i=1;$i<=7;$i++)
				{
					$positif=$i-$premierJour+10*$jour-10;
					if ($positif<0 || $jour>$jourMax)
						echo "\n\t\t<td class=\"none\">";
					if (($i>=$premierJour || $j>=2) && $jour<=$jourMax)
					{
						$event=false;
						for ($n=0; $n<$nbEvenement; $n++)
						{
							if ($jour==$jourEvenement[$n])
							{
								$event=true;
								break;
							}
						}
						if ($event)
						{
							echo "\n\t\t<td>";
							echo '<a href="#" onClick="javascript:ajax(this, event)" name="'.$annee.'-'.$moisActuel.'-'.$jour.'">'.$evenement[$n].'</a>';
						}
						else
						{
							if ($i==6 || $i==7)
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo $jour;
							}
							elseif ($moisActuel==1 && $jour==1)								//1er janvier -> nouvel an
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "nouvel an";
							}
							elseif ($moisActuel==5 && $jour==1)								//1er mai -> f�te du travail
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "f�te du travail";
							}
							elseif ($moisActuel==5 && $jour==8)								//8 mai -> armistice WWII
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "armistice WWII";
							}
							elseif ($moisActuel==7 && $jour==14)							//14 juillet -> f�te nationale
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "f�te nationale";
							}
							elseif ($moisActuel==11 && $jour==11)							//11 novembre -> armistice WWI
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "armistice WWI";
							}
							elseif ($moisActuel==12 && $jour==25)							//25 d�cembre -> no�l
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "no�l";
							}
							elseif ($moisActuel==8 && $jour==15)							//15 ao�t -> assomption
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "assomption";
							}
							elseif ($moisActuel==11 && $jour==1)							//1er novembre -> toussaint
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "toussaint";
							}
							elseif ($moisActuel==$moisPaques && $jour==$jourPaques)			//Lundi de P�ques
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "lundi de P�ques";
							}
							elseif ($moisActuel==$moisAscension && $jour==$jourAscension)	//Jeudi de l'Ascension
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "jeudi de l'Ascension";
							}
							elseif ($moisActuel==$moisPentecote && $jour==$jourPentecote)	//Lundi de Pentec�te
							{
								echo "\n\t\t<td class=\"ferie\">";
								echo "lundi de Pentec�te";
							}
							else
							{
								echo "\n\t\t<td>";
								echo $jour;
							}
						}
						$jour++;
					}
					echo "</td>";
				}
				echo "\n\t</tr>";
				$j++;
			}
			echo "\n</tbody>\n";

?>
					</table>
				<div id="descriptionEvenement">
				</div>
			</div>
<?php

		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function divisionEuclidienne($reste, $diviseur, $i)
		{
			$quotient=0;
			while ($reste>=$diviseur)
			{
				$reste=$reste-$diviseur;
				$quotient++;
			}
			if ($i==0)
				return $quotient;
			else
				return $reste;
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=evenements\" >Les �v�nements</a>";
			echo " > ";
			$annee=date("Y");
			if (isset($_GET['mois']))
			{
				$mois=$_GET['mois'];
				if ($mois<date("m"))
					$annee++;
			}
			else
			{
				$mois=date("m");
			}
			echo "<a href=\"?module=evenements&mois=".$mois."\" >".$this->tableauMois[$mois-1]." ".$annee."</a>";
			echo "</p>";
			echo "</div>";
		}
		
	}