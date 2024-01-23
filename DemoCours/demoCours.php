
<?php
	// Url API
	$urlRecherche="https://geo.api.gouv.fr" ;
	
	// URLS
	//https://geo.api.gouv.fr/regions
	//https://geo.api.gouv.fr/regions/76
	//https://geo.api.gouv.fr/regions/76/departements
	//https://geo.api.gouv.fr/departements/
	//https://geo.api.gouv.fr/departements/12
	//https://geo.api.gouv.fr/departements/12/communes"
	//https://geo.api.gouv.fr/epcis/
	//https://geo.api.gouv.fr/epcis/241200187/communes
	
	//Tests : https://reqbin.com/
	
	function appelAPI($apiUrl) {
		// Interrogation de l'API
		// Retourne le srésultat de l'appel 
		
		$curl = curl_init();									// Initialisation

		curl_setopt($curl, CURLOPT_URL, $apiUrl);				// Url de l'API à appeler
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);			// Retour dans une chaine au lieu de l'afficher
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 		// Désactive test certificat
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		
		$result = curl_exec($curl);								// Exécution
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);	// Récupération statut 
		// Si 404  indique qu'un serveur ne peut pas trouver la ressource demandée
		// Si 200 c'est OK
		curl_close($curl);										// Cloture curl
		
		if ($http_status=="200") {								// OK, l'appel s'est bien passé
			return json_decode($result,true); 					// Retourne la collection 
		} else {
			$result=[]; 										// retourne une collection Vide
			return $result;
		}
	}
	
?>


<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<title>WEB avancé democours</title>

		<!-- Bootstrap CSS -->
		<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
		
		<!-- Lien vers mon css -->
		<link href="css/monStyle.css" rel="stylesheet">
	
	</head>
	<body>
		<div class="container">

			<?php 
			
				/////////////////////////////////////////////////////////////
				// https://calendrier.api.gouv.fr/jours-feries/
				/////////////////////////////////////////////////////////////
				/*
				echo "<h1>Liste des jours fériés de métropole 20 ans dans le passé et 5 ans dans le futur</h1>";
				$apiUrl="https://calendrier.api.gouv.fr/jours-feries/metropole.json";
				$joursFeries=appelAPI($apiUrl);
				// var_dump($joursFeries);
				
				echo "<table class='table table-striped table-bordered'>";
				foreach($joursFeries as $cle=>$joursFeries) {
					echo "<tr><td>".$cle."</td><td>".$joursFeries."</td></tr>";	
				}
				echo "</table>";
				*/
				
		
				/*
				echo "<h1>Liste des jours fériés pour 2023</h1>";
				$apiUrl="https://calendrier.api.gouv.fr/jours-feries/metropole/2023.json";
				$joursFeries=appelAPI($apiUrl);
				// var_dump($joursFeries);
				
				echo "<table class='table table-striped table-bordered'>";
				foreach($joursFeries as $cle=>$joursFeries) {
					echo "<tr><td>".$cle."</td><td>".$joursFeries."</td></tr>";	
				}
				echo "</table>";
				*/
				
				
				/////////////////////////////////////////////////////////////
				//https://hubeau.eaufrance.fr/api/v1/temperature/station
				/////////////////////////////////////////////////////////////
				/*
				echo "<h1>Liste des cours d'eau</h1>";
				$apiUrl="https://hubeau.eaufrance.fr/api/v1/temperature/station";
				$coursDeau=appelAPI($apiUrl);
				//var_dump($coursDeau);
				$datas=$coursDeau['data'];
				echo "<table class='table table-striped table-bordered'>";
				echo "<tr><th>code_station</th><th>libelle_station</th><th>uri_station</th><th>localisation</th><th>altitude</th><th>Longitude</th><th>Latitude</th></tr>";
				foreach($datas as $cle=>$data) {
					echo "<tr>";
						echo "<td>".$data['code_station']."</td>";
						echo "<td>".$data['libelle_station']."</td>";
						echo "<td><a href='".$data['uri_station']."' target='_blank'>Lien</a></td>";
						echo "<td>".$data['localisation']."</td>";
						echo "<td>".$data['altitude']."</td>";
						echo "<td>".$data['longitude']."</td>";
						echo "<td>".$data['latitude']."</td>";
						
					echo "</tr>";	
				}
				echo "</table>";
				*/
				
				

				
				/////////////////////////////////////////////////////////////			
				//https://hp-api.onrender.com/api/characters
				/////////////////////////////////////////////////////////////
				/*
				echo "<h1 class='centrer'>Personnages Harry Potter</h1>";
				$apiUrl="https://hp-api.onrender.com/api/characters";
				$personnages=appelAPI($apiUrl);
				//var_dump($personnages);
				echo "<table class='table table-striped table-bordered'>";
				echo "<tr><th>Name</th><th>gender</th><th>house</th><th>ancestry</th><th>actor</th><th>Photo</th></tr>";
				foreach($personnages as $personnage) {
					echo "<tr class='centrer'>";
						echo "<td>".$personnage['name']."</td>";
						echo "<td>".$personnage['gender']."</td>";
						echo "<td>".$personnage['house']."</td>";
						echo "<td>".$personnage['ancestry']."</td>";
						echo "<td>".$personnage['actor']."</td>";
						echo "<td><img src='".$personnage['image']."'></td>";
						
					echo "</tr>";	
				}
				echo "</table>";
				*/
				
				/////////////////////////////////////////////////////////////
				//https://thronesapi.com/api/v2/Characters
				/////////////////////////////////////////////////////////////
				/*
				echo "<h1 class='centrer'>Game of Thrones API</h1>";
				$apiUrl="https://thronesapi.com/api/v2/Characters";
				$personnages=appelAPI($apiUrl);
				//var_dump($personnages);
				echo "<div class='row'>";
					foreach($personnages as $personnage) {
						echo "<div class='col-xs-4 cadresCom hauteurMin'>";
							echo $personnage['fullName']."<br>";
							echo $personnage['title']."<br>";
							echo $personnage['family']."<br>";
							echo "<img src='".$personnage['imageUrl']."'>";
					 echo "</div>";	
					}
				echo "</div>";
				*/
				////////////////////////////////////////////////////////////
				
			?>

		</div>

	</body>
</html>