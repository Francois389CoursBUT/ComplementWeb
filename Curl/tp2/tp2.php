<?php
// Url API
$urlRecherche = "https://geo.api.gouv.fr";

// URLS
//https://geo.api.gouv.fr/regions
//https://geo.api.gouv.fr/regions/76
//https://geo.api.gouv.fr/regions/76/departements
//https://geo.api.gouv.fr/departements/
//https://geo.api.gouv.fr/departements/12
//https://geo.api.gouv.fr/departements/12/communes"
//https://geo.api.gouv.fr/epcis/
//https://geo.api.gouv.fr/epcis/241200187/communes

//var_dump($_GET);

function appelAPI($apiUrl)
{
    // Interrogation de l'API

    $curl = curl_init();                                    // Initialisation

    curl_setopt($curl, CURLOPT_URL, $apiUrl);                // Url de l'API à appeler
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);            // Retour dans une chaine au lieu de l'afficher
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);        // Désactive test certificat
    curl_setopt($curl, CURLOPT_FAILONERROR, true);

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
 * Fonction qui affiche une option dans un select
 * Si l'option est sélectionnée, on ajoute l'attribut selected
 * @param $value : valeur de l'option
 * @param $textDisplay : texte affiché dans l'option
 * @param bool $isSelected : booléen qui indique si l'option est sélectionnée
 */
function afficherOption($value, $textDisplay, bool $isSelected = false): string
{
    $result = '<option value="' . $value . '"';
    $result .= $isSelected ? " selected" : "";
    return $result . '>' . $textDisplay . '</option>';
}

$regions = appelAPI("https://geo.api.gouv.fr/regions");
$regionSelectionner = isset($_GET["region"]) ? htmlentities($_GET["region"]) : "";
$departementSelectionnne = isset($_GET["departement"]) ? htmlentities($_GET["departement"]) : "";
$communeSelectionner = isset($_GET["commune"]) ? htmlentities($_GET["commune"]) : "";

$departements = array();
$communes = array();

if ($communeSelectionner != "") {
    $infosCommune = appelAPI("https://geo.api.gouv.fr/communes/" . $communeSelectionner);
    if (isset($infosCommune["codeEpci"])) {
        $communesComCom = appelAPI("https://geo.api.gouv.fr/epcis/" . $infosCommune["codeEpci"] . "/communes");
        $infosComCom = appelAPI("https://geo.api.gouv.fr/epcis/" . $infosCommune["codeEpci"]);
    } else {
        $communesComCom = array();
        $infosComCom = array();
    }
    $region = appelAPI("https://geo.api.gouv.fr/regions/" . $infosCommune["codeRegion"]);
    $departement = appelAPI("https://geo.api.gouv.fr/departements/" . $infosCommune["codeDepartement"]);

    $departementSelectionnne = $infosCommune["codeDepartement"];
    $regionSelectionner = $infosCommune["codeRegion"];
}

// Si un département est sélectionné, on récupère les communes de ce département
if ($departementSelectionnne != "") {
    $communes = appelAPI("https://geo.api.gouv.fr/departements/" . $departementSelectionnne . "/communes");
    $departement = appelAPI("https://geo.api.gouv.fr/departements/" . $departementSelectionnne);
    $regionSelectionner = $departement["codeRegion"];
}

// Si une région est sélectionnée, on récupère les départements de cette région
if ($regionSelectionner != "") {
    $departements = appelAPI("https://geo.api.gouv.fr/regions/" . $regionSelectionner . "/departements");
}

//Si n'y qu'un seul département d'ans une regions, on le sélectionne
if (sizeof($departements) == 1) {
    $departementSelectionnne = $departements[0]["code"];
    $communes = appelAPI("https://geo.api.gouv.fr/departements/" . $departementSelectionnne . "/communes");
    $communeSelectionner =  "";
}

// Si n'y qu'une seule commune dans un département, on la sélectionne
if (sizeof($communes) == 1) {
    $communeSelectionner = $communes[0]["code"];
    $infosCommune = appelAPI("https://geo.api.gouv.fr/communes/" . $communeSelectionner);
    if (isset($infosCommune["codeEpci"])) {
        $communesComCom = appelAPI("https://geo.api.gouv.fr/epcis/" . $infosCommune["codeEpci"] . "/communes");
        $infosComCom = appelAPI("https://geo.api.gouv.fr/epcis/" . $infosCommune["codeEpci"]);
    } else {
        $communesComCom = array();
        $infosComCom = array();
    }
    $region = appelAPI("https://geo.api.gouv.fr/regions/" . $infosCommune["codeRegion"]);
    $departement = appelAPI("https://geo.api.gouv.fr/departements/" . $infosCommune["codeDepartement"]);

    $departementSelectionnne = $infosCommune["codeDepartement"];
    $regionSelectionner = $infosCommune["codeRegion"];
}
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <title>WEB avancé TP2</title>

        <!-- Bootstrap CSS -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

        <!-- Lien vers mon css -->
        <link href="css/monStyle.css" rel="stylesheet">

    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 cadresCom">
                <h1>-- Recherche les informations d'une commune --</h1>
            </div>
            <?php
            ?>
            <div class="col-xs-4 cadresCom hauteurMin">
                <form action="tp2.php" method="GET">
                    <br/>
                    <label for="region">Région (<?php echo sizeof($regions) ?>) : </label>
                    <select name="region" class="form-control">
                        <option value="">Choisir une région</option>
                        <?php
                        sort($regions);
                        foreach ($regions as $regionListe) {
                            echo afficherOption($regionListe["code"], $regionListe["nom"], $regionListe["code"] == $regionSelectionner);
                        }
                        ?>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-block btn-primary">Afficher les départements de la région
                    </button>
                </form>
            </div>

            <div class="col-xs-4 cadresCom hauteurMin">

                <?php

                //                var_dump($departements);
                //                var_dump($departementSelectionner);
                // Si une région est sélectionnée, on affiche les départements de cette région
                if ($regionSelectionner != "") {
                    ?>
                    <!-- Région remplie, on cherche le département -->
                    <form action="tp2.php" method="GET">
                        <br/>
                        <!-- On garde la région selectionné -->
                        <input hidden value="<?php echo $regionSelectionner ?>" name="region">
                        <!-- On garde la région selectionné -->
                        <label for="departement">Département (<?php echo sizeof($departements) ?>) : </label>
                        <select name="departement" class="form-control">
                            <option value="">Choisir un d&eacute;partement</option>
                            <?php
                            sort($departements);
                            foreach ($departements as $departement) {
                                echo afficherOption($departement["code"], $departement["nom"], $departement["code"] == $departementSelectionnne);
                            }
                            ?>
                        </select>
                        <br>
                        <button type="submit" class="btn btn-block btn-primary">Afficher les communes du département
                        </button>
                    </form>
                    <?php
                }
                ?>
            </div>

            <div class="col-xs-4 cadresCom hauteurMin">

                <?php
                // Si un département est sélectionné, on affiche les communes de ce département
                if ($departementSelectionnne != "") {
                    ?>
                    <!-- département rempli, on cherche la commune -->
                    <form action="tp2.php" method="GET">
                        <br/>
                        <!-- On garde la région et le département selectionné -->
                        <input hidden value="<?php echo $regionSelectionner ?>" name="region">
                        <input hidden value="<?php echo $departementSelectionnne ?>" name="departement">
                        <label for="commune">Commune (<?php echo sizeof($communes) ?>) : </label>
                        <select name="commune" class="form-control">
                            <option value="">Choisir une commune</option>
                            <?php
                            sort($communes);
                            foreach ($communes as $commune) {
                                echo afficherOption($commune["code"], $commune["nom"], $commune["code"] == $communeSelectionner);
                            }
                            ?>
                        </select>
                        <br>
                        <button type="submit" value="Rechercher" class="btn btn-block btn-primary">Afficher les
                            informations
                            de la commune
                        </button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
        <br><br>


        <?php
        // Commune remplie, on affiche les renseignements
        if ($communeSelectionner != "") {

            ?>
            <div class="row">
                <div class="col-xs-12 cadresCom hauteurMinResultat">
                    <div class='row '>
                        <div class='col-xs-12 '>
                            <h1><?php echo $infosCommune["nom"] ?></h1>
                        </div>
                        <div class='col-xs-4 cadreAGauche'>
                            Région : <a
                                    href='tp2.php?region=<?php echo $infosCommune["codeRegion"] ?>'><?php echo $infosCommune["codeRegion"] . " - " . $region["nom"] ?></a><br/><br/>
                            Département : <a
                                    href='tp2.php?departement=<?php echo $infosCommune["codeDepartement"] ?>'><?php echo $infosCommune["codeDepartement"] . " - " . $departement["nom"] ?></a><br/><br/>
                            Commune : <?php echo $infosCommune["code"] . " - " . $infosCommune["nom"] ?><br/><br/>
                            Code SIREN : <?php echo $infosCommune["siren"] ?><br/>
                        </div>
                        <?php
                        if (isset($infosCommune["codeEpci"])) {
                            ?>
                            <div class='col-xs-4 cadreAGauche'>
                                Communauté de communes : <br>
                                <?php echo $infosComCom["nom"] ?><br>
                                <?php echo $infosComCom["population"] ?> habitants<br><br>
                                Communes :
                                <ul>
                                    <?php
                                    foreach ($communesComCom as $commune) {
                                        echo "<li><a href='tp2.php?commune=" . $commune["code"] . "'>" . $commune["nom"] . "</a></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                        }
                        ?>
                        <div class='col-xs-4'><?php echo $infosCommune["population"] ?> habitants<br/><br/>
                            Codes postaux :
                            <ul>
                                <?php
                                foreach ($infosCommune["codesPostaux"] as $codePostal) {
                                    echo "<li>" . $codePostal . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <br><br>
    </body>
</html>