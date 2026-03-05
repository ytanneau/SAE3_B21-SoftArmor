import DataGraph from "./DataGraph.js";
                import {TimeMilli,Compare,SideMonth,SideDay} from "./DataTime.js";
                import MakeGraph from "./MakeGraph.js";


                let sec1 = document.getElementById("sectionGen");

                let sec2 = document.getElementById("section_cat");
                sec2.style.display = "None";
                let sec3 = document.getElementById("section_prod");
                sec3.style.display = "None";

                document.getElementById("general").addEventListener('click', () => {
                    sec1.style.display = "initial";
                    sec2.style.display = "None";
                    sec3.style.display = "None";
                });
                document.getElementById("categories").addEventListener('click', () => {
                    sec1.style.display = "None";
                    sec2.style.display = "initial";
                    sec3.style.display = "None";
                    createCatChart();
                });
                document.getElementById("produits").addEventListener('click', () => {
                    sec1.style.display = "None";
                    sec2.style.display = "None";
                    sec3.style.display = "initial";
                });

                let tempY = DataGraph.createTempleteV2('Y'); 
                let tempM = DataGraph.createTempleteV2('M');
                let tempW = DataGraph.createTempleteV2('W');
                let tempD =DataGraph.createTempleteV2('D');
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
                let myChart;
                let CatChart;

                let typeG = 'bar';
                let offsetG = true;

                let graphCat ;
                let tabCat;

                let dataPourCategorie

                let categories=[];


                fetch('./json_prod.php?categorie=true')
                .then(response => response.json())
                .then(data => {
                    data.forEach(element => {
                        categories.push(element.nom_categorie);
                        
                        
                    });

                });

                fetch('./json_prod.php?id_compte=<?= $_SESSION['id_compte']?>')
                .then(response => response.json())
                .then(data => {

                    dataPourCategorie = data;

                    data = DataGraph.formate(data);

                    //initialise toutes les data pour les different graphes
                    dataY = DataGraph.groupByTime(data,"M",tempY);
                    dataM = DataGraph.groupByTime(data,"D",tempM);
                    dataW = DataGraph.groupByTime(data,"D",tempW);
                    dataD = DataGraph.groupByTime(data,"h",tempD);
                    dataH = DataGraph.groupByTime(data,"m",tempH);

                    datas = dataY;

                    typeG = 'bar';

                    abscisse = SideMonth.start(SideMonth.lesMois.at(datas.at(0).date.getMonth()));
                    //set les valeurs pour l'ordonnées
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

                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;

                    //affiche le graphe
                    myChart = new Chart(document.getElementById("a"), {
                        type: typeG,   // le type du graphique
                        data: {        // les données
                            labels: abscisse.reverse(),
                            datasets: [{
                                        label: 'Ventes',
                                        data: tab.reverse()
                                    }]
                        },
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: Math.max(...tab),
                                    suggestedMin: 0
                                },
                                x: {
                                    offset : offsetG
                                }
                            },
                            plugins: {
                                legend: {
                                display: false
                                }
                            }
                        }
                    });




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


                function createCatChart() {
                    //creer les option pour les categories
                    categories.forEach(element => {
                        let elm = document.createElement('option');
                        elm.value=`${element}`;
                        elm.innerText=element;
                        document.getElementById("filtreCat").append(elm);
                        
                    });
                    
                    //affiche le graphe
                    CatChart = new Chart(document.getElementById("b"), {
                        type: 'pie',
                        data: {
                            labels: categories,
                            datasets: [{
                                data: tabCat
                            }]
                        }
                    });
                }
                function deleteChart(chart,idCanva) {
                    chart.destroy();
                    let elm = document.createElement('canvas');
                    elm.id=idConteneur;
                    document.getElementById(`${idConteneur}_container`).append(elm);
                    
                }
                function createChart(id,typeChart,labelsChart,dataChart) {
                    new Chart(document.getElementById(id), {
                        type: typeChart,
                        data: {
                            labels: labelsChart,
                            datasets: [{
                                data: dataChart
                            }]
                        }
                    });
                }
                document.getElementById("filtreCat").addEventListener('change',()=>{
                    let type = document.getElementById("filtreCat").value;
                    deleteChart(CatChart,"b");
                    createChart("b",'pie',type,dataChart);
                });

                document.getElementById("filtreOrd").addEventListener('change',()=>{
                    //recup type pour ordonnée
                    valOrd=document.getElementById("filtreOrd").value;
                    
                    //reinitialise les données
                    tab = [];

                    //set la valeur pour orodnnée
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
                    myChart.destroy();
                    let elm = document.createElement('canvas');
                    elm.id="a";
                    document.getElementById("a_container").append(elm);
                    
                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;

                    //ajoute le graphe
                    myChart = new Chart(document.getElementById("a"), {
                        type: typeG,   // le type du graphique
                        data: {        // les données
                            labels: abscisse,
                            datasets: [{
                                        label: 'Ventes',
                                        data: tab.reverse()
                                    }]
                            },
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: Math.max(...tab),
                                    suggestedMin: 0
                                },
                                x: {
                                    offset : offsetG
                                }
                            },
                            plugins: {
                                legend: {
                                display: false
                                }
                            }
                        }
                    });

                });
                
                

                document.getElementById("filtreAbs").addEventListener('change',()=>{
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
                    myChart.destroy();
                    let elm = document.createElement('canvas');
                    elm.id="a";
                    document.getElementById("a_container").append(elm);

                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;
                            
                    //ajoute le graphe
                    myChart = new Chart(document.getElementById("a"), {
                        type: typeG,   // le type du graphique
                        data: {        // les données
                            labels: abscisse.reverse(),
                            datasets: [{
                                        label: 'Ventes',
                                        data: tab.reverse()
                                    }]
                            }
                        ,
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: Math.max(...tab),
                                    suggestedMin: 0
                                },
                                x: {
                                    offset : offsetG
                                }
                            },
                            plugins: {
                                legend: {
                                display: false
                                }
                            }
                        }
                    });
                });
                    
                
                document.getElementById("typeGraph").addEventListener('change',()=>{
                    //elenve lancien graphe et met un nouveau canvas
                    myChart.destroy();
                    let elm = document.createElement('canvas');
                    elm.id="a";
                    document.getElementById("a_container").append(elm);

                    //recup le type de graphe
                    typeG = document.getElementById("typeGraph").value;
                            
                    //ajoute le graphe
                    myChart = new Chart(document.getElementById("a"), {
                        type: typeG,   // le type du graphique
                        data: {        // les données
                            labels: abscisse,
                            datasets: [{
                                        label: 'Ventes',
                                        data: tab
                                    }]
                            }
                        ,
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: Math.max(...tab),
                                    suggestedMin: 0
                                },
                                x: {
                                    offset : offsetG
                                }
                            },
                            plugins: {
                                legend: {
                                display: false
                                }
                            }
                        }
                    });
                    
                });
                
                

                

