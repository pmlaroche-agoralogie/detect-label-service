<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Démonstration</title>
</head>

<body>
<div align="center"><h2 style="color:#FF6600;">Démonstration Label Finder</h2>

<?php
if (!isset($_POST['url'])){

	echo '<form name="form1" method="POST" action="">
	  <p><strong>URL de l\'image : </strong><input name="url" type="text" id="url" size="100" value="http://lesherbonautes.mnhn.fr/tiles/icedig/1124_B_10_0002884.jpg"></p>
	  <p><strong>Indice de confiance : </strong><input name="confiance" type="text" id="confiance" value="65" size="8"></p>
	  <p><input type="submit" name="button" id="button" value="Envoyer"></p>
	</form>';

}else{
	//http://label-finder.agoralogie.fr/find.php?source=http://lesherbonautes.mnhn.fr/tiles/icedig/1124_B_10_0002884.jpg&key=klxqjhfsgf&fileout=1
	
	// tester si l'image existe
	$file_headers = @get_headers(trim($_POST['url']));
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		echo '<p style="color:#FF6600;"><strong>ERREUR : Image non trouvée</strong></p>';
		echo '<form name="form1" method="POST" action="">
	  	<p><strong>URL de l\'image : </strong><input name="url" type="text" id="url" size="100" value="'.trim($_POST['url']).'"></p>
	 	 <p><strong>Indice de confiance : </strong><input name="confiance" type="text" id="confiance" value="'.$_POST['confiance'].'" size="8"></p>
	 	 <p><input type="submit" name="button" id="button" value="Envoyer"></p>
		</form>';
	}else {

	
		$fichier_a_appeler = "http://label-finder.agoralogie.fr/find.php?source=".trim($_POST['url'])."&key=klxqjhfsgf&fileout=1&confidence=".$_POST['confiance'];
	
		$retour_file = file($fichier_a_appeler);

		$url_censored = "";
		$area_list = "";
		$success = "";
		$message = "";
	
		$tab_file = json_decode($retour_file[0]);
		foreach ($tab_file as $field => $valeur) {
			if ($field=="url"){
				$url_censored = $valeur;
			}
			if ($field=="area_list"){
				$area_list = $valeur;
			}	
			if ($field=="success"){
				$success = $valeur;
			}	
			if ($field=="message"){
				$message = $valeur;
			}	
		}
		echo "<p><strong>Réponse retournée : </strong><textarea name='textarea' id='textarea' cols='100' rows='5'>".$retour_file[0]."</textarea></p>";
		$url_source = str_replace("censored","source",$url_censored);
		echo "<p><strong>Sucess : </strong>".$success."</p>";
		if ($message != ""){
			echo "<p><strong>Message : </strong>".$message."</p>";
		}
		if ($success==1){
			echo '<p><strong>Image source : </strong><a href="'.$url_source.'" target="_blank">'.$url_source.'</a></p>';
			echo '<p><strong>Image retouchée : </strong><a href="'.$url_censored.'" target="_blank">'.$url_censored.'</a></p>';
			echo '<p><strong>Nombre d\'étiquettes traitées : </strong>'.count(json_decode($area_list)).'</p>';
			echo "<p><strong>Coordonnées traitées : </strong>".$area_list."</p>";
			$url_detect = str_replace("censored","detect",$url_censored);
			echo "<table><tr><td><img src='".$url_detect."' width='300px' style='border:1px solid #333333;'/></td><td style='padding-left:30px;'><img src='".$url_censored."' width='300px;' style='border:1px solid #333333;' /></td></tr></table>";
		}
		echo "<p style='color:#FF6600;'><strong>&larr; <a style='color:#FF6600;' href='http://label-finder.agoralogie.fr/demo.php'>Saisir une autre URL</a></strong></p>";
	}
}
?>    
</div>
</body>

</html>
