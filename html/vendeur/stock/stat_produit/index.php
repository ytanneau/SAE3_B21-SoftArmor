<?php

define("HOME_GIT", "../../../../");
define("HOME_SITE", "../../../");

if (!isset($_SESSION)) {
    session_start();
}

// Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
    header('location: ' . HOME_SITE);
    exit;
}
// Sinon si je ne suis pas connecté, retour à la page connexion vendeur
else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_produit.php';

//verif si le produit appartien au vendeur
if(vendeur_verif_produit($_GET["id_produit"],$_SESSION["id_compte"])){

    $nom = detail_produit($_GET["id_produit"])["nom_stock"];
}
else{
    header('location: ../');
    exit;
}
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="../../statistiques/dist/chart.umd.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>

    <title>Statistiques</title>
</head>
<body>
    <?php include "../../header.php" ?>
    <a href="../../accueil"><img src="../../../image/retour.svg" class = "fleche_produit_arriere"></a>

    <section id="section_prod">
        <h1>Statistiques <?php echo $nom; ?></h1>
        <label>Tirer Les</label>
        <select class="produit" id="filtreOrdProd">
            <option value="qte">Quantité</option>
            <option value="prix">Prix</option>
            <option value="nbAchat">Nombres D'achats</option>
        </select>
        <label>Sur Les</label>
        <select class="produit" id="filtreAbsProd">
            <option value="M">12 Derniers Mois</option>
            <option value="W">30 Derniers Jours</option>
            <option value="D">7 Derniers Jours</option>
            <option value="h">24 Dernieres Heures</option>
            <option value="m">60 Dernieres Minutes</option>
        </select>
        <label>Diagramme en </label>
        <select class="produit" id="typeGraphProd">
            <option value="bar">Barre</option>
            <option value="line">Ligne</option>
        </select>
        <div id="c_container">
            <canvas id="c"></canvas>
        </div>
    </section>
    <script type="module" >
        import DataGraph from "../../statistiques/DataGraph.js";
        import {TimeMilli,Compare,SideMonth,SideDay} from "../../statistiques/DataTime.js";
        import MakeGraph from "../../statistiques/MakeGraph.js";

        /*  
            ---------------------
            ------FONCITONS------
            ---------------------
        */

        //supprimer un graphe
        function deleteChart(chart,idCanva) {
            chart.destroy();
            document.getElementById(idCanva).remove();
            let elm = document.createElement('canvas');
            elm.id=idCanva;
            document.getElementById(`${idCanva}_container`).append(elm);
            
        }

        //creer un graphe
        function createChart(id,typeChart,labelsChart,dataChart,offsetChart="auto",displayLegend=true,isClickable=false, displayScales=true) {
            let options = {
                    plugins: {
                        legend: {
                            display: displayLegend
                        }
                    },
                    
                    scales: {
                        y: {
                            display: displayScales,
                            suggestedMax: Math.max(...dataChart),
                            suggestedMin: 0,
                            grid: {
                                display: displayScales,
                            }
                        },
                        x: {
                            display: displayScales,
                            offset : offsetChart,
                            grid: {
                                display: displayScales,
                            }
                        }
                    }
                }

            if(isClickable){
                options.onClick = (event, elements) => {
                    if (elements.length > 0) {
                        const graphe = event.chart;
                        const index = elements[0].index;
                        const label = event.chart.data.labels[index];
                        const value = [];
                        const plage = document.getElementById('filtreAbsCat').value;
                        
                        labelGraph = [];
                        let valOrd = document.getElementById("filtreOrdCat").value;

                        let methodeDonne;
                        switch (valOrd) {
                            case "qte":
                                methodeDonne = "quantite"
                            break;

                            case "nbAchat":
                                methodeDonne = "nb_commande"
                            break;

                            case "prix":
                                methodeDonne = "prix"
                            break;

                        }
                        
                        if(tabAssociatifCat[label].length != 0){

                            tabAssociatifCat[label].forEach(element => {
                                let initialValue = 0;
                                value.push(getTimeData(graphCat.resetData().filtreByCategorie([element]),plage).value[methodeDonne].reduce(
                                        (accumulator, currentValue) => accumulator + currentValue,
                                        initialValue,)
                                    );
                                labelGraph.push(element);
                            });
                            labelGraph.push(`Autres ${label}`);
                            let initialValue = 0;
                            value.push(event.chart.data.datasets[0].data[index] - value.reduce(
                                        (accumulator, currentValue) => accumulator + currentValue,
                                        initialValue,));
                            deleteChart(graphe,id);

                            createChart(id,typeChart,labelGraph,value,offsetChart,displayLegend, true, false);
                        }else{
                            let lstProdCat = getTimeData(graphCat.resetData().filtreByCategorie([label]),plage).value[methodeDonne];
                            console.log(label,lstProdCat);
                        }
                    }
                }
            }

            return new Chart(document.getElementById(id), {
                type: typeChart,
                data: {
                    labels: labelsChart,
                    datasets: [{
                        data: dataChart
                    }]
                },
                options: options
            });
        }


        //creer le graphe pour la section "produit"
        function createProdChart() {

            //s'il est affiché => supprimer
            if(ProdChart !== null){
                deleteChart(ProdChart,"c");
            }
            
            let valAbs = document.getElementById("filtreAbsProd").value;
            let valOrd = document.getElementById("filtreOrdProd").value;
            let type = document.getElementById("typeGraphProd").value;

            tabCat = [];
        
            let methodePeriode;
            let methodeDonne;

            switch (valAbs) {
                case "M":
                    methodePeriode = "getYear"
                break;

                case "W":
                    methodePeriode = "getMonth"
                break;

                case "D":
                    methodePeriode = "getWeek"
                break;

                case "h":
                    methodePeriode = "getDay"
                break;

                case "m":
                    methodePeriode = "getHour"
                break;
            }

            switch (valOrd) {
                case "qte":
                    methodeDonne = "quantite"
                break;

                case "nbAchat":
                    methodeDonne = "nb_commande"
                break;

                case "prix":
                    methodeDonne = "prix"
                break;

            }
            
            let dataBase = graphCat.resetData().filtreByProduit(<?php echo ($_GET["id_produit"])?>)[methodePeriode]();
            let produit = dataBase.value[methodeDonne];
            let label = dataBase.label;
            
            //affiche le graphe
            ProdChart = createChart("c", type, label, produit, "auto", false, false, true);
        }


        function getTimeData(graph,plage){
            switch(plage){
                case "M": return graph.getYear();
                case "W": return graph.getMonth();
                case "D": return graph.getWeek();
                case "h": return graph.getDay();
                case "m": return graph.getHour();
            }
        }

        let valOrd = "qte";
        let valAbs = "M";

        let ProdChart = null;
        let labelGraph = [];

        let graphCat ;
        let tabCat;

        let dataPourProduit;         

        // récupère les produits du vendeur
        fetch('../../statistiques/json_prod.php?id_compte=<?= $_SESSION['id_compte']?>')
        .then(response => response.json())
        .then(data => {
            
            graphCat = new MakeGraph(data);
            createProdChart();


        })
        .catch(error => {
            console.error('Erreur :', error);
        });

        //changer filtre produit
        document.querySelectorAll(".produit").forEach((element)=>{
            element.addEventListener('change',()=>{
                createProdChart();
            });
        });


    </script>
</body>
</html>