<?php

	class Galerie extends ModuleAbstrait {
	
		function Galerie() {
		}
		
		
		
		function preTraitement($action) {
			switch($action) {
				case 'suppression':
					if ($_SESSION['connect'])
					{
						if (isset($_POST['dir']))
							$directory=$_POST['dir'];
						else
							$directory="";
						$path="galerie/".$directory;
						$dossier=opendir($directory);
						while ($file=readdir($dossier))
						{
							$nomFichier=substr($file, 0, strlen($file)-4);
							$path=$directory."/".$nomFichier;
							if (isset($_POST[$nomFichier]))
							{
								if ($_POST[$nomFichier]=='on')
								{
									unlink($path.".jpg");
									if (file_exists($path."_petit.jpg"))
									{
										unlink($path."_petit.jpg");
									}
								}
							}
						}
						closedir($dossier);
					}
					break;
				
				default:
					break;
			}
		}
		
		
		
		function afficherPage() {
			$this->navigation();
			echo "<div id=\"galerie\">";
			
			if (isset($_GET['dir']))
			{
				$dossier=opendir("galerie");
				if (is_dir("galerie/".$_GET['dir']))
				{
					$directory=$_GET['dir'];
				}
				else
				{
					$directory="";
				}
				closedir($dossier);
			}
			else
			{
				$directory="";
			}
			
			$path="galerie/".$directory;
			$dossier=opendir($path);
			$j=0;
			$k=0;
			$alt=array("");
			$dir=array("Retour");
			while ($file=readdir($dossier))
			{
				if ($file!="." && $file!="..")
				{
					$n=strlen($file)-10;
					$extension_petit=substr($file, $n, 10);
					$n=strlen($file)-4;
					$extension=substr($file, $n, 4);
					if ($extension==".jpg" && $extension_petit!="_petit.jpg")
					{
						$alt[$j]=substr($file, 0, $n);
						$j++;
					}
					else if (is_dir($path."/".$file))
					{
						$dir[$k]=$file;
						$k++;
					}
				}
			}
			closedir($dossier);
			
			
			
			$detail=0;
			if (isset($_GET['photo']))
			{
				$dossier=opendir($path);
				if (is_file($path."/".$_GET['photo'].".jpg"))
				{
					$i=0;
					while ($alt[$i]!=$_GET['photo'] && $i!=count($alt))
					{
						$i++;
					}
					if ($i!=count($alt) || $alt[$i]==$_GET['photo'])
					{
						echo "<p>";
						if ($i!=0)
							echo "<a href=\"?module=galerie&dir=".$directory."&photo=".$alt[$i-1]."\" class=\"left\" >image précédante </a>";
						if ($i!=(count($alt)-1))
							echo "<a href=\"?module=galerie&dir=".$directory."&photo=".$alt[$i+1]."\" class=\"right\" > image suivante</a>";
						echo "</p>";
							
						$taille=getimagesize($path."/".$alt[$i].".jpg");
						if ($taille[0]<750)
							echo "<img src=\"".$path."/".$alt[$i].".jpg\" alt=\"".$alt[$i]."\" />";
						else
							echo "<img src=\"".$path."/".$alt[$i].".jpg\" alt=\"".$alt[$i]."\" width=\"750\" />";
						
						echo "<p>";
						if ($i!=0)
							echo "<a href=\"?module=galerie&dir=".$directory."&photo=".$alt[$i-1]."\" class=\"left\" >image précédante </a>";
						if ($i!=(count($alt)-1))
							echo "<a href=\"?module=galerie&dir=".$directory."&photo=".$alt[$i+1]."\" class=\"right\" > image suivante</a>";
						echo "</p>";
					}
					$detail=1;
				}
				else
				{
					$detail=0;
				}
				closedir($dossier);
			}
			
			
			if ($detail==0)
			{
				/********affichage du choix des pages********/
				$imageParPage=16; //multiple de 4
				if (!isset($_GET['page']))
				{
					$image=0;
					$pageActuelle=1;
				}
				else
				{
					$image=$imageParPage*($_GET['page']-1);
					$pageActuelle=$_GET['page'];
				}
				$pagesTotales=ceil((count($alt)+count($dir))/$imageParPage);
				$pages=$pagesTotales;
				echo '<p>';
				if ($pageActuelle!=1)
				{
					$pagePrec=$pageActuelle-1;
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pagePrec."\" title=\"page précédante\"><</a>&nbsp&nbsp;";
				}
				echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=1\" title=\"première page\">1..</a>&nbsp&nbsp;";
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
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$i."\">".$i."</a>&nbsp&nbsp;";
				}
				if ($pagesTotales!=1)
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pagesTotales."\" title=\"dernière page\">..".$pagesTotales."</a>&nbsp&nbsp;";
				if ($pageActuelle!=$pagesTotales)
				{
					$pageSuiv=$pageActuelle+1;
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pageSuiv."\" title=\"page suivante\">></a>";
					}
				echo "\n<p/>";
				
				/********affichage des images********/
				if ($_SESSION['connect'])
				{
					echo "<form action=\"?module=galerie&action=suppression\" method=\"post\">";
				}
				echo "<table>";
				$j=0;
				$k=0;
				for ($i=$image; $i<($image+$imageParPage); $i++)
				{
					if ($j==0 || $j==4 || $j==8 || $j==12)
						echo "<tr>";
					if ($i<(count($dir)))
					{
						if (isset($dir[$i]))
							echo "<td><a href=\"?module=galerie&dir=".$dir[$i]."\"><img src=\"galerie/dossier_petit.jpg\" alt=\"".$dir[$i]."\" /></a>";
							echo "<br/>".$dir[$i];
					}
					else
					{
						if (isset($alt[$i]))
						{
							echo "<td><a href=\"?module=galerie&dir=".$directory."&photo=".$alt[$i]."\"><img src=\"".$path."/".$alt[$i]."_petit.jpg\" alt=\"".$alt[$i]."\" /></a>";
							if ($_SESSION['connect']==1)
							{
								echo "<br/><input type=\"checkbox\" name=\"".$alt[$i]."\"/> supprimer";
								echo "<input type=\"hidden\" name=\"dir\" value=\"<?".$directory."\" />";
							}
							echo "</td>";
						}
					}
					if ($j==3 || $j==7 || $j==11 || $j==15)
						echo "</tr>";
					$j++;
				}
				echo "</table>";
				if ($_SESSION['connect'])
				{
					echo "<p><input type =\"submit\" value=\"supprimer les photos cochées\" /><input type =\"reset\" /></p>";
					echo "</form>";
				}
				
				
				/********ré-affichage du choix des pages********/
				if (!isset($_GET['page']))
				{
					$image=0;
					$pageActuelle=1;
				}
				else
				{
					$image=$imageParPage*($_GET['page']-1);
					$pageActuelle=$_GET['page'];
				}
				$pagesTotales=ceil((count($alt)+count($dir))/$imageParPage);
				$pages=$pagesTotales;
				echo '<p>';
				if ($pageActuelle!=1)
				{
					$pagePrec=$pageActuelle-1;
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pagePrec."\" title=\"page précédante\"><</a>&nbsp&nbsp;";
				}
				echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=1\" title=\"première page\">1..</a>&nbsp&nbsp;";
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
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$i."\">".$i."</a>&nbsp&nbsp;";
				}
				if ($pagesTotales!=1)
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pagesTotales."\" title=\"dernière page\">..".$pagesTotales."</a>&nbsp&nbsp;";
				if ($pageActuelle!=$pagesTotales)
				{
					$pageSuiv=$pageActuelle+1;
					echo "\n\t<a href=\"?module=galerie&dir=".$directory."&page=".$pageSuiv."\" title=\"page suivante\">></a>";
					}
				echo "\n<p/>";
				
				echo "\n<p>page ".$pageActuelle."</p>\n";
			}
			
			
			echo "</div>";
		}
		
		
		
		function afficherMenuDroite() {
		}
		
		
		
		function navigation() {
			echo "<div id=\"navigation\">";
			echo "<p>";
			echo "<a href=\"?module=galerie\" >Galerie</a>";
			
			if (isset($_GET['dir']))
			{
				$dossier=opendir("galerie");
				if (is_dir("galerie/".$_GET['dir']))
				{
					$directory=$_GET['dir'];
				}
				else
				{
					$directory="";
				}
				closedir($dossier);
			}
			else
			{
				$directory="";
			}
			if ($directory!="")
			{
				echo " > ";
				echo "<a href=\"?module=galerie&dir=".$directory."\" >".$directory."</a>";
			}
			
			$path="galerie/".$directory;
			if (isset($_GET['photo']))
			{
				$dossier=opendir($path);
				if (is_file($path."/".$_GET['photo'].".jpg"))
				{
					echo " > ";
				echo "<a href=\"?module=galerie&dir=".$directory."&photo=".$_GET['photo']."\" >".$_GET['photo']."</a>";
					
				}
				closedir($dossier);
			}
			
			echo "</p>";
			echo "</div>";
		}
		
		
	}

?>