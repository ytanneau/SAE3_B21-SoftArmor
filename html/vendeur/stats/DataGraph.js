import {TimeMilli, Compare} from "./DataTime.js";


export default class DataGraph {

    static formate(data) {
        let res = [];
        data.forEach(ele => {
            let v = ele;
            v.date_commande = new Date(ele.date_commande);
            res.push(v);
        });
        return res;
    };

    static filtreByProduit(data, id_produit) {
        if (!Array.isArray(data)) {
            console.error("data is not a Array");
        }
        else {
            return data.filter((ele) => {
                return ele.id_produit == id_produit;
            });
        }
    };

    static filtreByCategorie() {

    };

    static groupByTime(data, type, templete = createTemplete(type)) {

        if (type == 'P') {

        }
        else {

            if (type == 'm') {
                templete.forEach(ele => {
                    let liste = data.filter(ele => {
                        return Compare.CompareMinute(ele.date, data.date);
                    });
                    ele.prix = this.sommePrix(liste);
                    ele.quantite = this.sommeQuantiter(liste);
                });
            }
            else if (type == 'h') {
                templete.forEach(ele => {
                    let liste = data.filter(ele => {
                        return Compare.CompareHour(ele.date, data.date);
                    });
                    ele.prix = this.sommePrix(liste);
                    ele.quantite = this.sommeQuantiter(liste);
                });
            }
            else if (type == 'D') {
                templete.forEach(ele => {
                    let liste = data.filter(ele => {
                        return Compare.compareMinute(ele.date, data.date);
                    });
                    ele.prix = this.sommePrix(liste);
                    ele.quantite = this.sommeQuantiter(liste);
                });
            }
            else if (type == 'W' || type == 'M') {
                templete.forEach(ele => {
                    let liste = data.filter(ele => {
                        return Compare.CompareDate(ele.date, data.date);
                    });
                    ele.prix = this.sommePrix(liste);
                    ele.quantite = this.sommeQuantiter(liste);
                });
            }
            else if (type == 'Y') {
                templete.forEach(ele => {
                    let liste = data.filter(ele => {
                        return Compare.CompareMonth(ele.date, data.date);
                    });
                    ele.prix = this.sommePrix(liste);
                    ele.quantite = this.sommeQuantiter(liste);
                });
            }
            else {
                console.error("Fonc (group) parametre type");
                return -1;
            }
            return templete;
        }
    };

    static createTemplete(type) {
        let res = [];
        let now = new Date();
        //now.setMilliseconds(0);
        //now.setSeconds(0);

        /*if (type == 'm') {
            for (let i = 0; i < 60; i++) {
                res.push({"date": now.valueOf()-(UNE_MINUTE*i),"quantite":0,"prix":0})
            };
        }*/
        if (type == 'h') {
            //now.setMinutes(0);
            for (let i = 0; i < 60; i++) {
                //res.push({"date": now.valueOf()-(UNE_MINUTE*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf() - (TimeMilli.UNE_MINUTE * i)), "quantite": 0, "prix": 0 })
            };
        }
        else if (type == 'D') {
            //now.setHours(0);
            for (let i = 0; i < 24; i++) {
                //res.push({"date": now.valueOf()-(UNE_HEURE*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf() - (TimeMilli.UNE_HEURE * i)), "quantite": 0, "prix": 0 })
            };
        }
        else if (type == 'W') {
            //now.setDate(0);
            for (let i = 0; i < 7; i++) {
                //res.push({"date": now.valueOf()-(UN_JOUR*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf() - (TimeMilli.UN_JOUR * i)), "quantite": 0, "prix": 0 })
            };
        }
        else if (type == 'M') {
            for (let i = 0; i < 30; i++) {
                //res.push({"date": now.valueOf()-(UN_JOUR*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf() - (TimeMilli.UN_JOUR * i)), "quantite": 0, "prix": 0 })
            };
        }
        else if (type == 'Y') {
            for (let i = 0; i < 12; i++) {
                //res.push({"date": now.valueOf()-(UN_MOIS*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf() - (TimeMilli.UN_MOIS * i)), "quantite": 0, "prix": 0 })
            };
        }
        else {
            console.error("Fonc (createTemplete) parametre type");
        }
        return res;
    }


    static createTempletePersonaliser(duree, uniter, fin, debut = undefined) {
        //tout en milliseconde
        if (duree === undefined) {
            duree = fin - debut;
        }
        else if (fin === undefined) {
            fin = duree + debut;
        }
        else if (debut === undefined) {
            debut = fin - duree;
        }

        if (uniter > duree) {
            console.error("uniter > duree");
            return -1;
        }
        if (debut > fin) {
            console.error("debut > fin");
            return -1;
        }
        if (uniter <= 0 || duree <= 0 || debut < 0 || fin <= 0) {
            console.error("valeu parametre");
            return -1;
        }

        let res = [];
        let now = new Date();

        for (let index = fin; index > debut; index -= uniter) {
            //res.push({"date": index,"quantite":0,"prix":0});
            res.push({ "date": new Date(index), "quantite": 0, "prix": 0 });
        }

        return res;
    }

    static sommePrix(data) {
        let somme = 0;
        data.forEach(ele => {
            somme += ele.prix;
        });
    }

    static sommeQuantiter(data) {
        let somme = 0;
        data.forEach(ele => {
            somme += ele.quantite;
        });
    }
};