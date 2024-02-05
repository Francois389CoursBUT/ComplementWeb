<?php
// Url API
$urlRecherche = "https://geo.api.gouv.fr";

// URLS exemples
//https://geo.api.gouv.fr/regions
//https://geo.api.gouv.fr/regions/76
//https://geo.api.gouv.fr/regions/76/departements
//https://geo.api.gouv.fr/departements/
//https://geo.api.gouv.fr/departements/12
//https://geo.api.gouv.fr/departements/12/communes"
//https://geo.api.gouv.fr/communes/12202
//https://geo.api.gouv.fr/epcis/
//https://geo.api.gouv.fr/epcis/241200187/communes

function appelAPI($apiUrl)
{
    // Interrogation de l'API
    // Retourne le résultat en format JSON
    $curl = curl_init();                                    // Initialisation

    curl_setopt($curl, CURLOPT_URL, $apiUrl);                // Url de l'API à appeler
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);            // Retour dans une chaine au lieu de l'afficher
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);        // Désactive test certificat
    curl_setopt($curl, CURLOPT_FAILONERROR, true);

    // À utiliser sur le réseau des PC IUT, pas en WIFI, pas sur une autre connexion
    $proxy = "http://cache.iut-rodez.fr:8080";
    curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
    curl_setopt($curl, CURLOPT_PROXY, $proxy);
    ///////////////////////////////////////////////////////////////////////////////
    $result = curl_exec($curl);                                // Exécution
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);    // Récupération statut
    // Si 404  indique qu'un serveur ne peut pas trouver la ressource demandée
    // Si 200 c'est OK

    curl_close($curl);                                        // Cloture curl

    if ($http_status == "200") {                                // OK, l'appel s'est bien passé
        return json_decode($result, true);                    // Retourne la collection
    } else {
        // retourne une collection Vide
        return [];
    }
}

/**
 * @param $communes
 * @return void
 */
function afficherListe($communes)
{
    echo "<ul>";
    for ($j = 0; $j < count($communes); $j++) {
        echo "<li>" . $communes[$j]["code"] . " " . $communes[$j]["nom"] . "</li>";
    }
    echo "</ul>";
}

?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <title>WEB avancé TP1</title>

        <!-- Bootstrap CSS -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

    </head>
    <body>
    <?php
    // Variables à utiliser dans le script
    $occitanie = "Occitanie";
    $lozere = "Lozère";
    $trelans = "Trélans";
    $marseille = "Marseille";
    $bordeaux = "Bordeaux";
    $massegros = "Massegros Causses Gorges";
    $saintGermainDuTeil = "Saint-Germain-du-Teil";

    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Liste des r&eacute;gions de France</h1>
                <?php
                $regions = appelAPI($urlRecherche . "/regions");

                sort($regions);

                afficherListe($regions);
                ?>
            </div>

            <div class="col-xs-12">
                <h1>Liste des d&eacute;partements de la r&eacute;gion <?php echo $occitanie; ?></h1>
                <?php
                $codeRegion = "";
                $trouve = false;
                for ($i = 0; $i < count($regions) && !$trouve; $i++) {
                    if ($regions[$i]["nom"] == $occitanie) {
                        $codeRegion = $regions[$i]["code"];
                        $trouve = true;
                    }
                }
                $departements = appelAPI($urlRecherche . "/regions/" . $codeRegion . "/departements");
                sort($departements);

                afficherListe($departements);

                ?>
            </div>

            <div class="col-xs-12">
                <h1>Liste des Communes de la <?php echo $lozere; ?></h1>

                <?php
                $codeDepartement = "";
                $trouve = false;
                $departements = appelAPI($urlRecherche . "/departements");
                for ($i = 0; $i < count($departements) && !$trouve; $i++) {
                    if ($departements[$i]["nom"] == $lozere) {
                        $codeDepartement = $departements[$i]["code"];
                        $trouve = true;
                    }
                }

                $communes = appelAPI($urlRecherche . "/departements/" . $codeDepartement . "/communes");
                sort($communes);

                afficherListe($communes);

                ?>

            </div>

            <div class="col-xs-12">
                <h1>Dans quelle communauté de communes est la commune de <?php echo $trelans; ?></h1>
                <?php
                $trouve = false;
                $codeEpciCommune = "";
                for ($i = 0; $i < count($communes) && !$trouve; $i++) {
                    if ($communes[$i]["nom"] == $trelans) {
                        $codeEpciCommune = $communes[$i]["codeEpci"];
                        $trouve = true;
                    }
                }
                $communauteCommunes = appelAPI($urlRecherche . "/epcis/" . $codeEpciCommune);
                echo $communauteCommunes["nom"];
                ?>
            </div>

            <div class="col-xs-12">
                <h1>Liste des communes de la communauté de communes de <?php echo $trelans; ?></h1>
                <?php
                $communesCommunaute = appelAPI($urlRecherche . "/epcis/" . $codeEpciCommune . "/communes");
                sort($communesCommunaute);

                afficherListe($communesCommunaute);

                ?>
            </div>
            <div class="col-xs-12">
                <h1>Nombre d'habitants de la commune de '<?php echo $saintGermainDuTeil; ?>'</h1>
                <?php

                $trouve = false;
                $population = "";
                for ($i = 0; $i < count($communesCommunaute) && !$trouve; $i++) {
                    if ($communesCommunaute[$i]["nom"] == $saintGermainDuTeil) {
                        $population = $communesCommunaute[$i]["population"];
                        $trouve = true;
                    }
                }
                echo $population . " habitants";
                ?>

            </div>
            <div class="col-xs-12">
                <h1>Liste des codes postaux de '<?php echo $massegros; ?>'</h1>
                <?php
                $trouve = false;
                $codePostale = [];
                for ($i = 0; $i < count($communesCommunaute) && !$trouve; $i++) {
                    if ($communesCommunaute[$i]["nom"] == $massegros) {
                        $codePostale = $communesCommunaute[$i]["codesPostaux"];
                        $trouve = true;
                    }
                }
                sort($codePostale);

                echo "<ul>";
                for ($j = 0; $j < count($codePostale); $j++) {
                    echo "<li>" . $codePostale[$j] . "</li>";
                }
                echo "</ul>";
                ?>
            </div>
        </div>
    </div>
    <br><br>
    </body>
</html>