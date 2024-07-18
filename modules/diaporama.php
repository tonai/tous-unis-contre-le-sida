<?php

	class Diaporama extends ModuleAbstrait {
	
		var $path = "galerie/affiche";
		
		function Diaporama() {
		}
		
		
		
		function preTraitement($action) {
		}
		
		
		
		function afficherPage() {
		}
		
		
		
		function afficherMenuDroite() {
		$this->redimensionnement();
		$this->javascript();

?>
				<div class="bandeau" >
					Diaporama
				</div>
				<div id="diaporama">
					<img src="style/logo.jpg" alt="diaporama" name="defil" style="filter:progid:DXImageTransform.Microsoft.Fade(Overlap=1.00);" />
				</div>
<?php

		}
		
		
		
		function redimensionnement() {
			$dossier=opendir($this->path);
			$j=0;
			while ($file=readdir($dossier))
			{
				if ($file!="." && $file!="..")
				{
					$n=strlen($file)-10;
					$extension_petit=substr($file, $n, 10);
					$fin=strlen($file)-4;
					$extension=substr($file, $fin, 4);
					
					if ($extension==".jpg")
					{
						if (!isset($src[$j]))
							$src[$j]="";
						if (!isset($src_petit[$j]))
							$src_petit[$j]="";
						if ($extension_petit=="_petit.jpg")
						{
							$existe=0;
							for ($k=0;$k<$j;$k++)
							{
								$titre=substr($file, 0, $n);
								if ($src[$k]==$titre)
								{
									$src_petit[$k]=$titre;
									$existe=1;
								}
							}
							if ($existe==0)
							{
								$titre=substr($file, 0, $n);
								$src_petit[$j]=$titre;
								$j++;
							}
						}
						else
						{
							$existe=0;
							for ($k=0;$k<$j;$k++)
							{
								$titre=substr($file, 0, $fin);
								if ($src_petit[$k]==$titre)
								{
									$src[$k]=$titre;
									$existe=1;
								}
							}
							if ($existe==0)
							{
								$titre=substr($file, 0, $fin);
								$src[$j]=$titre;
								$j++;
							}
						}
					}
				}
			}
			closedir($dossier);
			
			for($i=0;$i<$j;$i++)
			{
				if($src[$i]!=$src_petit[$i])
				{
					$fond = imagecreatetruecolor(150, 150);
					$background = imagecolorallocate($fond, 255, 255, 255);
					imagefill($fond, 0, 0, $background);
					$source = imagecreatefromjpeg("".$this->path."/".$src[$i].".jpg");
					$largeur_source = imagesx($source);
					$hauteur_source = imagesy($source);
					
					if($largeur_source>$hauteur_source)
					{
						$largeur_destination = 150;
						$hauteur_destination = ceil($largeur_destination*$hauteur_source/$largeur_source);
						$position_X=0;
						$position_Y=(150-$hauteur_destination)/2;
					}
					else
					{
						$hauteur_destination = 150;
						$largeur_destination = ceil($hauteur_destination*$largeur_source/$hauteur_source);
						$position_Y=0;
						$position_X=(150-$largeur_destination)/2;
					}
					$destination = imagecreatetruecolor($largeur_destination, $hauteur_destination);

					imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
					imagecopy($fond, $destination, $position_X, $position_Y, 0, 0, $largeur_destination, $hauteur_destination);
					$nom="".$this->path."/".$src[$i]."_petit.jpg";
					imagejpeg($fond, $nom);
				}
			}
		}
		
		
		
		function javascript() {
			$dossier=opendir($this->path);
			$j=0;
			while ($file=readdir($dossier))
			{
				if ($file!="." && $file!="..")
				{
					$n=strlen($file)-10;
					$extension_petit=substr($file, $n, 10);
					if ($extension_petit=="_petit.jpg")
					{
						$diaporama[$j]=$file;
						$j++;
					}
				}
			}
			closedir($dossier);

?>
			<script language="javascript">
imgPath = new Array;
if (document.images)
{
<?php

	for($i=0;$i<$j;$i++)
	{
		echo "\ni".$i." = new Image;";
		echo "\ni".$i.".src = '".$this->path."/".$diaporama[$i]."';";
		echo "\nimgPath[".$i."] = i".$i.".src;";
	}

?>
}
a = 0;

function ejs_img_fx(img)
{
	if(img && img.filters && img.filters[0])
	{
		img.filters[0].apply();
		img.filters[0].play();
	}
}

function defilimg()
{
	a = Math.floor(Math.random() * (<?php echo $j; ?> - 1)) + 1;
	if (document.images)
	{
		ejs_img_fx(document.defil)
		document.defil.src = imgPath[a];
		tempo3 = setTimeout("defilimg()",10000);
		a++;
	}
}
		</script>
<?php

		}
		
		
		
		function navigation() {
		}
	}

?>