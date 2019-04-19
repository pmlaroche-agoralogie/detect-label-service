<?php
//url de test http://label-finder.agoralogie.fr/find.php?source=http://lesherbonautes.mnhn.fr/tiles/icedig/1124_B_10_0002884.jpg&confidence=77.4&key=klxqjhfsgf&fileout=0
if ($_GET['key'] !='klxqjhfsgf'){
  	$retourappel["success"] = 0;
  	$retourappel["message"] = "Incorrect key";
	echo json_encode($retourappel); 
  	die();
}
if ($_GET['fileout'] == "1") $fileout=1;else $fileout=0;

$url = $_GET['source'];
// Remove all illegal characters from a url
$url = filter_var($url, FILTER_SANITIZE_URL);
// Validate url
if (!(filter_var($url, FILTER_VALIDATE_URL) !== false)) {
  	$retourappel["success"] = 0;
  	$retourappel["message"] = "Url not valid";
	echo json_encode($retourappel); 
  	die();
}

//compute a unique name for the temporary file
include "class_uuid.php"; 
$filename=UUID::v4(); 

$limite_confiance = ($_GET['confidence'] *100) /100; //to be sure it is a number

$returnvalue = shell_exec('/var/www/html/execute_modele.sh'.' '.$url.' '.$filename);

$retourappel = array();
if ($returnvalue == 1 ){
        // pour les tests : 
       // echo '<a href=http://label-finder.agoralogie.fr/data/list_zone_'.$filename.'.txt>zones</a><br>';
       // echo '<img src=http://label-finder.agoralogie.fr/data/sred_'.$filename.'.jpg><br>';
       // echo '<img src=http://label-finder.agoralogie.fr/data/detect_'.$filename.'.jpg><br>';
        
        //calculer la liste de zones limitŽe aux premieres zones ( > indice confiance) dans var/www/html/data/list_zone_'.$filename.'.txt
		$current = file_get_contents('http://label-finder.agoralogie.fr/data/list_zone_'.$filename.'.txt');
		$liste = trim($current);
		$liste = str_replace('[[','[',$liste);
		$liste = str_replace(']]',']',$liste);
		
		$liste = str_replace("\n","",$liste); 
		$liste = str_replace("\r","",$liste); 
		$liste = str_replace("\t","",$liste); 	
	
		$liste = str_replace('  ',' ',$liste);
			
		$tab_liste = explode("] [", $liste);
		
		$tab_result = array();
		for ($i=0; $i<count($tab_liste); $i++){
	
			$zone_cour = $tab_liste[$i];
			$zone_cour = str_replace('[','',$zone_cour);
			$zone_cour = str_replace(']','',$zone_cour);
		
			$tab_coordonnees = explode(" ", $zone_cour);
	
			$ma_chaine = '';
			for ($z=0; $z<count($tab_coordonnees); $z++){
				if ($z != 4){
					$coord = $tab_coordonnees[$z];
					$val = ((int)($coord*10000))/10000;
					$ma_chaine .= ",".$val;
					if (isset($limite_confiance)){
						if ($z==5 && ($val*100) >= $limite_confiance){
							$tab_rectangle[] = substr($ma_chaine,1);
						}
					}
				}
			}
			//$tab_result[] = substr($ma_chaine,1);
		}

	 	$retourappel["area_list"]=json_encode($tab_rectangle);
	
        
        // calculer l'image avec zone blanche si $fileout==1
        if($fileout==1){
            //calcule une image avec les zones (> confiance)blanchies depuis le fichier source				
			///var/www/html/data/source_$filename.jpg
			if (count($tab_rectangle)){
				traite_image("/var/www/html/data/source_".$filename.".jpg",$tab_rectangle,"/var/www/html/data/censored_".$filename.".jpg");
    	        $retourappel["url"]="http://label-finder.agoralogie.fr/data/censored_".$filename.".jpg";			   
			}else{
			   $retourappel["url"]="http://label-finder.agoralogie.fr/data/source_".$filename.".jpg";
			}			 
		}
        $retourappel["success"]=1;
       
    }else{
        $retourappel["success"]=0;
        $retourappel["message"]="echec script analyse";
    }

	echo json_encode($retourappel, JSON_FORCE_OBJECT); 
	
function traite_image($nom_image,$tab_rectangle,$new_nom_image){

	$source = imagecreatefromjpeg($nom_image);
	$largeur_source = imagesx($source);
	$hauteur_source = imagesy($source);
	$white = imagecolorallocate($source, 255, 255, 255);

	// par exemple : $tab_rectangle = array("0.8332,0,0.9977,0.5035,0.9521","0.9304,0.788,0.9956,1,0.2563");
	// pour chaque élément du tableau (pour chaque rectangle)
	for ($i=0; $i<count($tab_rectangle); $i++){
		$coordonnees = $tab_rectangle[$i];
		$tab_coordonnees = explode(",", $coordonnees);
		$y1_pourcent = $tab_coordonnees[0];
		$x1_pourcent = $tab_coordonnees[1];
		$y2_pourcent = $tab_coordonnees[2];
		$x2_pourcent = $tab_coordonnees[3];	

		$x1 = $largeur_source * $x1_pourcent;
		$x2 = $largeur_source * $x2_pourcent;
		$y1 = $hauteur_source * $y1_pourcent;
		$y2 = $hauteur_source * $y2_pourcent;
		
		// Dessine un rectangle blanc
		imagefilledrectangle($source, $x1, $y1, $x2, $y2, $white);
			
	}
	
	imagejpeg($source,$new_nom_image); //enregistre une nouvelle image
	imagedestroy($source); //détruit l'image, libérant ainsi de la mémoire


}	
?>
