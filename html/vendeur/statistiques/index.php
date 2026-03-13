<?php
    
    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");

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

    require_once HOME_GIT .".config.php";
    require_once(HOME_GIT . 'fonction_alarme.php');

    //permet d'utiliser le fichier config.php
    /*require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';*/
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <script src="dist/chart.umd.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include HOME_SITE . 'link_head.php'; ?>
        <title>Alizon - Accueil vendeur</title>
    </head>
    <body class="stat">
        <?php include "../header.php" ?>
        <main>
            <section>
                <button id="general">General</button>
                <button id="categories">Categories</button>
                <button id="produits">Produits</button>
                <button id="autre">Personnalisé</button>
            </section>
            <section id="sectionGen">
                <h1>Statistiques Générales</h1>
                
                <label>Tirer Les</label>
                <select id="filtreOrd">
                    <option value="qte">Quantité</option>
                    <option value="prix">Prix</option>
                    <option value="nbAchat">Nombres D'achats</option>
                </select>
                <label>Sur Les</label>
                <select id="filtreAbs">
                    <option value="M">12 Derniers Mois</option>
                    <option value="W">30 Derniers Jours</option>
                    <option value="D">7 Derniers Jours</option>
                    <option value="h">24 Dernieres Heures</option>
                    <option value="m">60 Dernieres Minutes</option>
                </select>
                <label>Diagramme en </label>
                <select id="typeGraph">
                    <option value="bar">Barre</option>
                    <option value="line">Ligne</option>
                </select>
                <div id="a_container">
                    <canvas id="a"></canvas>
                </div>
                    
            </section>
            <section id="section_cat">
                <h1>Statistiques Par Catégories</h1>
                <label>Categories</label>
                <select id="filtreCat"></select>
                <label>Tirer Les</label>
                <select id="filtreOrdCat">
                    <option value="qte">Quantité</option>
                    <option value="prix">Prix</option>
                    <option value="nbAchat">Nombres D'achats</option>
                </select>
                <label>Sur Les</label>
                <select id="filtreAbsCat">
                    <option value="M">12 Derniers Mois</option>
                    <option value="W">30 Derniers Jours</option>
                    <option value="D">7 Derniers Jours</option>
                    <option value="h">24 Dernieres Heures</option>
                    <option value="m">60 Dernieres Minutes</option>
                </select>
                <button id='resetCat'>Reset Filtre</button>
                <span class="aide" data-tooltip="Cliquez sur une catégorie dans le diagramme pour afficher toutes les sous-catégories, ou tous les produits dans cette catégorie"></span>
                <div id="b_container">
                    <canvas id="b"></canvas>
                </div>
            </section>
            <section id="section_prod">
                <h1>Statistiques Par Produits</h1>
            </section>
            
            <script type="module">
                import DataGraph from "./DataGraph.js";
                import {TimeMilli,Compare,SideMonth,SideDay} from "./DataTime.js";
                import MakeGraph from "./MakeGraph.js";

                //supprimer un graphe
                function deleteChart(chart,idCanva) {
                    chart.destroy();
                    document.getElementById(idCanva).remove();
                    let elm = document.createElement('canvas');
                    elm.id=idCanva;
                    document.getElementById(`${idCanva}_container`).append(elm);
                    
                }

                /*  
                    ---------------------
                    ------FONCITONS------
                    ---------------------
                */


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
                                
                                if(tabAssociatifCat[label].length != 0){

                                    tabAssociatifCat[label].forEach(element => {

                                        let initialValue = 0;
                                        value.push(getTimeData(graphCat.resetData().filtreByCategorie([element]),plage).value.prix.reduce(
                                                (accumulator, currentValue) => accumulator + currentValue,
                                                initialValue,)
                                            );
                                    });

                                    tabAssociatifCat[label].push("Autres");
                                    let initialValue = 0;
                                    value.push(event.chart.data.datasets[0].data[index] - value.reduce(
                                                (accumulator, currentValue) => accumulator + currentValue,
                                                initialValue,));
                                    console.log(index,label,value,tabAssociatifCat[label],);
                                    deleteChart(graphe,id);

                                    createChart(id,typeChart,tabAssociatifCat[label],value,offsetChart,displayLegend, displayScales);
                                    tabAssociatifCat[label].pop();
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

                // créer un graphe pour la section "générale"
                function createGenChart() {
                    //recup type pour abscisse
                    valAbs=document.getElementById("filtreAbs").value;

                    //recup type pour ordonnée
                    valOrd=document.getElementById("filtreOrd").value;
                    
                    //reinitialise les données
                    tab = [];

                    //set la valeur pour abscisse
                    abscisse = [];
                    switch (valAbs) {
                        case "M" :
                            typeG = 'bar';
                            datas = dataY;
                            abscisse = SideMonth.start(SideMonth.lesMois.at(datas.at(0).date.getMonth()));
                            // abscisse.push(...MakeGraph.lesMois);
                        break;
                
                        case "W":
                            typeG = 'line';
                            abscisse.push("Auj");
                            for (let i = 1; i < 31; i++) {
                                abscisse.push(`J -${i}`);   
                            }
                            datas = dataM;
                        break;

                        case "D":
                            datas = dataW;
                            typeG = 'bar';
                            abscisse = SideDay.start(SideDay.lesJour.at(datas.at(0).date.getDay()));
                            // abscisse.push(...MakeGraph.lesJour);
                        break;

                        case "h":
                            datas = dataD;
                            abscisse.push("Auj");
                            typeG = 'line';
                            for (let i = 1; i < 25; i++) {
                                abscisse.push(`-${i}h`); 
                            }
                        break;

                        case "m":
                            typeG = 'line';
                            abscisse.push("Auj");
                            datas = dataH;
                            for (let i = 1; i < 61; i++) {
                                abscisse.push(`-${i}m`);   
                            }
                        break;

                        
                    } 

                    //set les datas pour la ordonnées
                    switch (valOrd) {
                        case 'qte':
                            datas.forEach(element => {
                                tab.push(element.quantite);
                            });
                        break;
                
                        case 'prix':
                            datas.forEach(element => {
                                tab.push(element.prix);
                            });
                        break;

                        case 'nbAchat':
                            datas.forEach(element => {
                                tab.push(element.nb_commande);
                            });
                        break;
                        
                    }

                    //elenve lancien graphe et met un nouveau canvas
                    
                    if (myChart != null) {
                        deleteChart(myChart,"a");
                    }

                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;
                            
                    //ajoute le graphe
                    myChart = createChart("a",typeG,abscisse.reverse(),tab.reverse(),offsetG,false);
                }

                //creer le graphe pour la section "catégories"
                function createCatChart() {

                    //s'il est affiché => supprimer
                    if(CatChart !== null){
                        deleteChart(CatChart,"b");
                    }

                    let valAbs = document.getElementById("filtreAbsCat").value;


                    tabCat = [];
                    categories.forEach(element => {

                        let methodePeriode;

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

                        let initialValue = 0;
                        let quantiteCat = graphCat.resetData().filtreByCategorie([element])[methodePeriode]().value.prix.reduce(
                                (accumulator, currentValue) => accumulator + currentValue,
                                initialValue,);
                        
                        tabAssociatifCat[element].forEach(elt => {
                            initialValue = 0;
                            quantiteCat+= graphCat.resetData().filtreByCategorie([element])[methodePeriode]().value.prix.reduce(
                                (accumulator, currentValue) => accumulator + currentValue,
                                initialValue,);
                        });
                        tabCat.push(quantiteCat);

                    });

                    //affiche le graphe
                    CatChart = createChart("b", "pie", categories, tabCat, "auto", true, true, false);
                    document.getElementById('b_container').style.setProperty("width", "40vw");
                }

                function createOnlyCatChart() {
                    let typeCat = document.getElementById("filtreCat").value;
                    let plage = document.getElementById("filtreAbsCat").value;
                    let typeVal = document.getElementById('filtreOrdCat').value;
                    
                    document.getElementById('b_container').style="none";
                    deleteChart(CatChart,"b");

                    // catégorie principale
                    let baseGraph = graphCat.resetData().filtreByCategorie([typeCat]);
                    let baseData = getTimeData(baseGraph,plage);


                    let values = getValueData(graphCat,typeVal,typeCat,plage);


                    // // ajouter les sous catégories
                    // if(tabAssociatifCat[typeCat]){
                    //     tabAssociatifCat[typeCat].forEach(subCat => {

                    //         let subGraph = graphCat.resetData().filtreByCategorie([subCat]);
                    //         let subData = getTimeData(baseGraph,plage);

                    //         subData.value.quantite.forEach((v,i)=>{
                    //             values[i] += v;
                    //         });

                    //     });
                    // }

                    CatChart = createChart(
                        "b",
                        "bar",
                        baseData.label,
                        values,
                        undefined,
                        false
                    );
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

                function getValueData(graph,typeVal,typeCat,plage){
                    let tableauAled = [typeCat];
                    switch (typeVal) {
                        case 'prix':
                            
                            tabAssociatifCat[typeCat].forEach(element => {
                                tableauAled.push(element);
                            });
                            
                            return getTimeData(graph.resetData().filtreByCategorie(tableauAled),plage).value.prix;
                            
                            break;
                        
                        case 'qte':
                            

                            tabAssociatifCat[typeCat].forEach(element => {
                                tableauAled.push(element);
                            });
                            
                            return getTimeData(graph.resetData().filtreByCategorie(tableauAled),plage).value.quantite;
                            
                            break;
                        
                        case 'nbAchat':
                            

                            tabAssociatifCat[typeCat].forEach(element => {
                                tableauAled.push(element);
                            });
                            
                            return getTimeData(graph.resetData().filtreByCategorie(tableauAled),plage).value.nb_commande;
                            
                            break;
                        
                    }
                }


                let sec1 = document.getElementById("sectionGen");

                let sec2 = document.getElementById("section_cat");
                sec2.style.display = "None";
                let sec3 = document.getElementById("section_prod");
                sec3.style.display = "None";
                
                function resetOptions() {
                    let listeSelects = document.getElementsByTagName('select');

                    for (let select of listeSelects) {
                        for (let option of select.options) {
                            option.selected = option.defaultSelected;
                        }
                    }
                }

                document.getElementById("general").addEventListener('click', () => {
                    sec1.style.display = "initial";
                    sec2.style.display = "None";
                    sec3.style.display = "None";

                    resetOptions();
                    createGenChart();
                });

                document.getElementById("categories").addEventListener('click', () => {
                    sec1.style.display = "None";
                    sec2.style.display = "initial";
                    sec3.style.display = "None";
                    createCatChart();

                    resetOptions();
                });

                document.getElementById("produits").addEventListener('click', () => {
                    sec1.style.display = "None";
                    sec2.style.display = "None";
                    sec3.style.display = "initial";

                    resetOptions();
                });


                let tempY = DataGraph.createTempleteV2('Y'); 
                let tempM = DataGraph.createTempleteV2('M');
                let tempW = DataGraph.createTempleteV2('W');
                let tempD = DataGraph.createTempleteV2('D');
                let tempH = DataGraph.createTempleteV2('h');

                let dataY;
                let dataM;
                let dataW;
                let dataD;
                let dataH;

                let valOrd = "qte";
                let valAbs = "M";

                let datas;
                let abscisse;
                let tab= [];
                var myChart;
                let CatChart = null;

                let typeG = 'bar';
                let offsetG = true;

                let graphCat ;
                let tabCat;

                let dataPourCategorie

                let categories=[];
                let toutecategories= [];
                let tabAssociatifCat= {};

                // récupère catégories
                fetch('./json_prod.php?categorie=true&id_compte=<?= $_SESSION['id_compte']?>')
                .then(response => response.json())
                .then(data => {
                    data.forEach(element => {
                        categories.push(element.nom_categorie);
                        
                    });
                });

                // récupère toutes les catégories (catégories enfants aussi)
                fetch('./json_prod.php?toutecategorie=true&id_compte=<?= $_SESSION['id_compte']?>')
                .then(response => response.json())
                .then(data => {
                    data.forEach(element => {
                        toutecategories.push(element.nom_categorie);
                        
                    });
                    
                    data.forEach(element => {
                        if(element.nom_categorie_sup !== null){
                            
                            if (!tabAssociatifCat[element.nom_categorie]) {
                                tabAssociatifCat[element.nom_categorie] = [];
                            }
                            if (!tabAssociatifCat[element.nom_categorie_sup]) {
                                tabAssociatifCat[element.nom_categorie_sup] = [];
                            }
                            
                            tabAssociatifCat[element.nom_categorie_sup].push(element.nom_categorie);
                            
                        }
                        else{
                            tabAssociatifCat[element.nom_categorie] = [];
                        }
                    });

                    //mettre les categories 
                    if(document.getElementById("filtreCat").children.length === 0){
                        
                        let elm = document.createElement('option');
                        elm.value=`all`;
                        elm.innerText="Toutes";
                        document.getElementById("filtreCat").append(elm);
                        
                        //creer les option pour les categories
                        toutecategories.forEach(element => {
                            
                            let elm = document.createElement('option');
                            elm.value=`${element}`;
                            elm.innerText=element;
                            document.getElementById("filtreCat").append(elm);
                            
                        });
                    }

                });

                // récupère les produits du vendeur
                fetch('./json_prod.php?id_compte=<?= $_SESSION['id_compte']?>')
                .then(response => response.json())
                .then(data => {
                    // STATISTIQUES GENERALES
                    dataPourCategorie = data;

                    data = DataGraph.formate(data);

                    //initialise toutes les data pour les different graphes
                    dataY = DataGraph.groupByTime(data,"M",tempY);
                    dataM = DataGraph.groupByTime(data,"D",tempM);
                    dataW = DataGraph.groupByTime(data,"D",tempW);
                    dataD = DataGraph.groupByTime(data,"h",tempD);
                    dataH = DataGraph.groupByTime(data,"m",tempH);

                    datas = data;
                    
                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;

                    //affiche le graphe
                    createGenChart();

                    // STATISTIQUES CATEGORIE
                    graphCat = new MakeGraph(dataPourCategorie);
                    tabCat = [];
                    categories.forEach(element => {
                        
                        let initialValue = 0;
                        tabCat.push(graphCat.resetData().filtreByCategorie([element]).getYear().value.prix.reduce(
                                (accumulator, currentValue) => accumulator + currentValue,
                                initialValue,)
                            );
                    });

                })
                .catch(error => {
                    console.error('Erreur :', error);
                });


                /*  
                    ----------------------
                    ------Categories------
                    ----------------------
                */

                //reset les filtres graphe categorie
                document.getElementById("resetCat").addEventListener('click',()=>{
                    createCatChart();
                    document.getElementById('b_container').style="width:40vw;";
                    const select = document.getElementById("filtreAbsCat");
                    const select2 = document.getElementById("filtreCat");

                    for (let option of select.options) {
                        option.selected = option.defaultSelected;
                    }

                    for (let option of select2.options) {
                        option.selected = option.defaultSelected;
                    }
                });

                //changer le type de graphe catégorie
                document.getElementById("filtreCat").addEventListener("change", () => {

                    let typeCat = document.getElementById("filtreCat").value;
                    let plage = document.getElementById("filtreAbsCat").value;
                    let typeVal = document.getElementById("filtreOrdCat").value;
                    

                    if(typeCat === "all"){
                        createCatChart();
                        return;
                    }
                    else {
                        createOnlyCatChart();
                    }
                    document.getElementById('b_container').style="none";
                    deleteChart(CatChart,"b");

                    // catégorie principale
                    let baseGraph = graphCat.resetData().filtreByCategorie([typeCat]);
                    let baseData = getTimeData(baseGraph,plage);
                    

                    let values = getValueData(baseGraph,typeVal,typeCat,plage);
                    

                    // // ajouter les sous catégories
                    // if(tabAssociatifCat[typeCat]){
                    //     tabAssociatifCat[typeCat].forEach(subCat => {

                    //         let subGraph = graphCat.resetData().filtreByCategorie([subCat]);
                    //         let subData = getTimeData(baseGraph,plage);

                    //         subData.value.prix.forEach((v,i)=>{
                    //             values[i] += v;
                    //         });

                    //     });
                    // }

                    CatChart = createChart(
                        "b",
                        "bar",
                        baseData.label,
                        values,
                            "auto",
                            false
                    );

                });
                
                //changer abscisse graphe categorie
                document.getElementById("filtreAbsCat").addEventListener("change", () => {
                    
                    const plage = document.getElementById("filtreAbsCat").value;
                    const typeCat = document.getElementById("filtreCat").value;
                    const typeVal = document.getElementById('filtreOrdCat').value;
                    //toutes les categories 
                    if(typeCat === "all"){
                        createCatChart();

                        tabCat = getValueData(graphCat,typeVal,typeCat,plage);
                        // tabCat = [];
                        // categories.forEach(element => {

                        //     let initialValue = 0;

                        //     let data = getTimeData(
                        //         graphCat.resetData().filtreByCategorie([element]),plage
                        //     ).value.prix;

                        //     let quantiteCat = data.reduce(
                        //         (accumulator, currentValue) => accumulator + currentValue,
                        //         initialValue
                        //     );

                        //     tabAssociatifCat[element].forEach(elt => {

                        //         let subData = getTimeData(
                        //             graphCat.resetData().filtreByCategorie([elt]),plage
                        //         ).value.prix;

                        //         quantiteCat += subData.reduce(
                        //             (accumulator, currentValue) => accumulator + currentValue,
                        //             0
                        //         );

                        //     });

                        //     tabCat.push(quantiteCat);

                        // });

                        deleteChart(CatChart,"b");

                        CatChart = createChart(
                            "b",
                            "pie",
                            categories,
                            tabCat,
                            "auto",
                            true,
                            true,
                            false
                        );

                    }

                    //une seule categorie
                    else{
                        createOnlyCatChart();
                    }
                });

                //changer l'ordonnee du graphique categorie
                document.getElementById("filtreOrdCat").addEventListener('change',()=>{

                    const plage = document.getElementById("filtreAbsCat").value;
                    const typeCat = document.getElementById("filtreCat").value;
                    const typeVal = document.getElementById('filtreOrdCat').value;

                    // if(typeCat === "all"){
                    //     createCatChart();
                    //     document.getElementById('b_container').style="width:40vw;";
                    //     return;
                    // }
                    
                    let graph = graphCat.resetData().filtreByCategorie([typeCat]);
                    let base = getTimeData(graph,plage);

                    let values = getValueData(graphCat,typeVal,typeCat,plage);
                    
                    //elenve lancien graphe et met un nouveau canvas
                    deleteChart(CatChart,"b");
                            
                    //ajoute le graphe
                    CatChart = createChart(
                            "b",
                            "bar",
                            base.label,
                            values,
                            "auto",
                            false
                        );
                    
                }); 
                

                /*  
                    ----------------------
                    ------Generales-------
                    ----------------------
                */

                //changer l'oronnée du graphique général
                document.getElementById("filtreOrd").addEventListener('change',()=>{
                    createGenChart();
                });

                //changer l'abscisse du graphique général
                document.getElementById("filtreAbs").addEventListener('change',()=>{
                    createGenChart();
                });
                    
                
                //changer type graphique general
                document.getElementById("typeGraph").addEventListener('change',()=>{
                    createGenChart();
                });


                //reset les filtres graphe categorie
                document.getElementById("resetCat").addEventListener('click',()=>{
                    resetOptions();
                    createCatChart();
                });

            </script>

            <script src="<?=HOME_SITE?>infobulle.js"></script>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>