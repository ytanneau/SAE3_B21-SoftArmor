export default class MakeGraph {

    static lesJour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    static lesMois = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];

    constructor(data) {
        this.data = this.formate(data)
        this.use = data;
    }

    formate(data) {
        let res = [];
        data.forEach(ele => {
            let v = ele;
            v.date = new Date(ele.date_commande);
            res.push(v);
        });
        return res;
    };

    resetData() {
        this.use = this.data;
        return this;
    }

    filtreByProduit(id_produit) {
        this.use = this.use.filter((ele) => {
            return ele.id_produit == id_produit;
        });
        return this;
    };

    filtreByCategorie(categos) {
        if (!Array.isArray(categos)) {
            console.error("categos is not a Array");
        }
        else {
            this.use = this.use.filter((ele) => {
                return categos.includes(ele.categorie);
            });
        }
        return this;
    };

    createTempleteV2(type) {
        let res = [];
        let now = new Date();

        now.setMilliseconds(0);
        now.setSeconds(0);
        if (type == 'h') {
            for (let i = 0; i < 60; i++) {
                let liste = this.use.filter(ele => {
                    return this.CompareMinute(now, ele.date);
                });
                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setMinutes(now.getMinutes() - 1));
            };
        }

        now.setMinutes(0);
        if (type == 'D') {
            for (let i = 0; i < 24; i++) {
                let liste = this.use.filter(ele => {
                    return this.CompareHour(now, ele.date);
                });
                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setHours(now.getHours() - 1));
            };
        }

        now.setHours(0);
        if (type == 'W') {
            for (let i = 0; i < 7; i++) {
                let liste = this.use.filter(ele => {
                    return this.CompareDate(now, ele.date);
                });

                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setDate(now.getDate() - 1));
            };
        }
        if (type == 'M') {
            for (let i = 0; i < 30; i++) {
                let liste = this.use.filter(ele => {
                    return this.CompareDate(now, ele.date);
                });
                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setDate(now.getDate() - 1));
            };
        }

        now.setDate(1);
        if (type == 'Y') {
            for (let i = 0; i < 12; i++) {
                let liste = this.use.filter(ele => {
                    return this.CompareMonth(now, ele.date);
                });
                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setMonth(now.getMonth() - 1));
            };
        }

        return this.formateValue(res.reverse());
    }

    formateValue(data) {
        let prix = [];
        let quantite = [];
        let nb = [];
        let moyenPrix = [];
        let moyenQuantie = [];

        data.forEach(ele => {
            prix.push(ele.prix);
            quantite.push(ele.quantite);
            nb.push(ele.nb_commande);
            if (ele.nb_commande == 0) {
                moyenPrix.push(0);
                moyenQuantie.push(0);
            }
            else {
                moyenPrix.push(ele.prix / ele.nb_commande);
                moyenQuantie.push(ele.quantite / ele.nb_commande);
            }
        });
        return {
            prix: prix,
            quantite: quantite,
            nb_commande: nb,
            moyenPrix: moyenPrix,
            moyenQuantie: moyenQuantie
        }
    }

    sommePrix(data) {
        let somme = 0;
        data.forEach(ele => {
            somme += ele.prix * ele.quantite;
        });
        return somme;
    }

    sommeQuantiter(data) {
        let somme = 0;
        data.forEach(ele => {
            somme += ele.quantite;
        });
        return somme;
    }


    next(ele, eles) {
        for (let index = 0; index < eles.length; index++) {
            if (ele == eles.at(index)) {

                if (index == 0) {
                    return eles.at(eles.length - 1);
                }
                else {
                    return eles.at(index - 1);
                }
            }
        }
    }

    start(ele, eles) {
        let res = [];
        res.push(ele);
        let e = ele;
        for (let index = 1; index < eles.length; index++) {
            e = this.next(e, eles);
            res.push(e);
        }
        return res.reverse();
    }

    CompareYear(date1, date2) {
        return (date1.getFullYear() == date2.getFullYear());
    }

    CompareMonth(date1, date2) {
        return (date1.getMonth() == date2.getMonth() && this.CompareYear(date1, date2));
    }

    CompareDate(date1, date2) {
        return (date1.getDate() == date2.getDate() && this.CompareMonth(date1, date2));
    }

    CompareHour(date1, date2) {
        return (date1.getHours() == date2.getHours() && this.CompareDate(date1, date2));
    }

    CompareMinute(date1, date2) {
        return (date1.getMinutes() == date2.getMinutes() && this.CompareHour(date1, date2));
    }

    getHour() {
        return {
            label: this.labelHour(),
            value: this.createTempleteV2('h')
        }
    }

    getDay() {
        return {
            label: this.labelDay(),
            value: this.createTempleteV2('D')
        }
    }

    getWeek() {
        return {
            label: this.start(MakeGraph.lesJour.at(new Date().getDay()), MakeGraph.lesJour),
            value: this.createTempleteV2('W')
        }
    }

    getMonth() {
        return {
            label: this.labelMonth(),
            value: this.createTempleteV2('M')
        }
    }

    getYear() {
        return {
            label: this.start(MakeGraph.lesMois.at(new Date().getMonth()), MakeGraph.lesMois),
            value: this.createTempleteV2('Y')
        }
    }

    labelHour() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getHours()}h${now.getMinutes()}`);
            now.setMinutes(now.getMinutes() - 1);
        }
        return res.reverse();
    }

    labelDay() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getHours()}h, (${MakeGraph.lesJour.at(now.getDay())})`);
            now.setHours(now.getHours() - 1);
        }
        return res.reverse();
    }

    labelMonth() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getDate()}/${now.getMonth() + 1}`);
            now.setDate(now.getDate() - 1);
        }
        return res.reverse();
    }


    //------------------------------------------------------------

    listeProduit() {
        res = [];
        this.use.forEach(element => {
            if (res.find(ele => {
                return ele == element.id_produit;
            }) == undefined) {
                ele.push(element.id_produit);
            }
        });
        return res;
    }

    nomProduit(id_produit) {
        return this.use.find(ele => {
            return ele.id_produit = id_produit;
        }).nom_stock;
    }

    getHourC(categos) {
        this.filtreByCategorie([categos]);
        let liste = this.listeProduit();

        let labels = [];
        let values = [];
        let prixs = [];
        let quantites = [];
        let commandes  = [];

        liste.forEach(id => {
            labels.push(this.nomProduit(id));

            let boj = new MakeGraph(this.data);
            boj.filtreByProduit(id);
            let val = boj.getHour().value;

            let initialValue = 0;
            prixs.push(val.prix.reduce(
                (accumulator, currentValue) => accumulator + currentValue,
                initialValue,
            ));

            initialValue = 0;
            quantites.push(val.quantite.reduce(
                (accumulator, currentValue) => accumulator + currentValue,
                initialValue,
            ));

            initialValue = 0;
            commandes.push(val.commande.reduce(
                (accumulator, currentValue) => accumulator + currentValue,
                initialValue,
            ));

        });

        return {
            label: labels,
            value: {
                prix: prixs,
                quantite: quantites,
                commande: commandes
            }
        }
    }

    getDayC(categorie) {
        return {
            label: this.labelDay(),
            value: this.createTempleteV2('D')
        }
    }

    getWeekC() {
        return {
            label: this.start(MakeGraph.lesJour.at(new Date().getDay()), MakeGraph.lesJour),
            value: this.createTempleteV2('W')
        }
    }

    getMonthC() {
        return {
            label: this.labelMonth(),
            value: this.createTempleteV2('M')
        }
    }

    getYearC() {
        return {
            label: this.start(MakeGraph.lesMois.at(new Date().getMonth()), MakeGraph.lesMois),
            value: this.createTempleteV2('Y')
        }
    }

    labelHourC() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getHours()}h${now.getMinutes()}`);
            now.setMinutes(now.getMinutes() - 1);
        }
        return res.reverse();
    }

    labelDayC() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getHours()}h, (${MakeGraph.lesJour.at(now.getDay())})`);
            now.setHours(now.getHours() - 1);
        }
        return res.reverse();
    }

    labelMonthC() {
        let now = new Date();
        let res = [];
        for (let index = 0; index < 60; index++) {
            res.push(`${now.getDate()}/${now.getMonth() + 1}`);
            now.setDate(now.getDate() - 1);
        }
        return res.reverse();
    }
}
