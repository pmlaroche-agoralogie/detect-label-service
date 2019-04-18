<?php
//url de test http://label-finder.agoralogie.fr/find.php?source=http://lesherbonautes.mnhn.fr/tiles/icedig/1124_B_10_0002884.jpg&confidence=77.4&key=klxqjhfsgf&fileout=0
if ($_GET['key'] !='klxqjhfsgf')die('Incorrect key');
if ($_GET['fileout'] == "1") $fileout=1;else $fileout=0;

$url = $_GET['source'];
// Remove all illegal characters from a url
$url = filter_var($url, FILTER_SANITIZE_URL);
// Validate url
if (!(filter_var($url, FILTER_VALIDATE_URL) !== false)) {
die("url not valid");
}

//compute a unique name for the temporary file
include "class_uuid.php"; 
$filename=UUID::v4(); 

$limite_confiance = ($_GET['confidence'] *100) /100; //to be sure it is a number

$returnvalue = shell_exec('/var/www/html/execute_modele.sh'.' '.$url.' '.$filename);

$retourappel = array();
if ($returnvalue == 1 )
    {
        // pour les tests : 
        echo '<a href=http://label-finder.agoralogie.fr/data/list_zone_'.$filename.'.txt>zones</a><br>';
        echo '<img src=http://label-finder.agoralogie.fr/data/sred_'.$filename.'.jpg><br>';
        echo '<img src=http://label-finder.agoralogie.fr/data/detect_'.$filename.'.jpg><br>';
        
        
        //calculer la liste de zones limitŽe aux premieres zones ( > indice confiance) dans var/www/html/data/list_zone_'.$filename.'.txt
        
        // calculer l'image avec zone blanche si $fileout==1
        if($fileout==1)
            {
                //calcule une image avec les zones (> confiance)blanchies depuis le fichier source
                $retourappel[url]="http://label-finder.agoralogie.fr/data/censored_".$filename.'.jpg';
            }
        
        $retourappel[success]=1;
        
        
    }
    else
    {
        $retourappel[success]=0;
        $retourappel[message]='echec script analyse';
    }
?>
