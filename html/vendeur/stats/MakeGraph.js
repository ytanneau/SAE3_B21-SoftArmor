export default class MakeGraph {

    static formate(data) {
        let res = [];
        data.forEach(ele => {
            let v = ele;
            v.date = new Date(ele.date_commande);
            res.push(v);
        });
        return res;
    };

    constructor(data) {
        this.data = MakeGraph.formate(data)
        this.use = data;
    }

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

    getWeek() {
        return {
            label: MakeGraph.start(MakeGraph.lesJour.at(new Date().getDay()), MakeGraph.lesJour),
            value: this.createTempleteV2('W')
        }
    }

    createTempleteV2(type) {
        let res = [];
        let now = new Date();
        now.setMilliseconds(0);
        now.setSeconds(0);

        if (type == 'h') {
            for (let i = 0; i < 60; i++) {
                res.push({ "date": new Date(now.valueOf()), "quantite": 0, "prix": 0, "nb_commande": 0, "nb_commande": 0 })
                now.setTime(now.setMinutes(now.getMinutes() - 1));
            };
        }
        now.setMinutes(0);
        if (type == 'D') {
            for (let i = 0; i < 24; i++) {
                //res.push({"date": now.valueOf()-(UNE_HEURE*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf()), "quantite": 0, "prix": 0, "nb_commande": 0, "nb_commande": 0 })
                now.setTime(now.setHours(now.getHours() - 1));
            };
        }
        now.setHours(0);
        if (type == 'W') {
            //now.setDate(0);
            for (let i = 0; i < 7; i++) {
                //res.push({"date": now.valueOf()-(UN_JOUR*i),"quantite":0,"prix":0})
                let liste = this.use.filter(ele => {
                    return this.CompareDate(now, ele.date);
                });
                res.push({ "date": new Date(now.valueOf()), "quantite": this.sommeQuantiter(liste), "prix": this.sommePrix(liste), "nb_commande": liste.length })
                now.setTime(now.setDate(now.getDate() - 1));
            };
        }
        if (type == 'M') {
            for (let i = 0; i < 30; i++) {
                //res.push({"date": now.valueOf()-(UN_JOUR*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf()), "quantite": 0, "prix": 0, "nb_commande": 0 })
                now.setTime(now.setDate(now.getDate() - 1));
            };
        }
        now.setDate(1);
        if (type == 'Y') {
            for (let i = 0; i < 12; i++) {
                //res.push({"date": now.valueOf()-(UN_MOIS*i),"quantite":0,"prix":0})
                res.push({ "date": new Date(now.valueOf()), "quantite": 0, "prix": 0, "nb_commande": 0 })
                now.setTime(now.setMonth(now.getMonth() - 1));
            };
        }
        return this.formateValue(res.reverse());
    }

    formateValue(data){
        let prix = [];
        let quantiter = [];
        let nb = [];

        data.forEach(ele => {
            prix.push(ele.prix);
            quantiter.push(ele.quantiter);
            nb.push(ele.nb_commande);
        });

        return{
            prix: prix,
            quantiter, quantiter,
            nb_commande : nb
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

    static lesJour = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    static lesMois = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];

    static next(ele, eles) {
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

    static start(ele, eles) {
        let res = [];
        res.push(ele);
        let e = ele;
        for (let index = 1; index < eles.length; index++) {
            e = this.next(e, eles);
            res.push(e);
        }
        return res.reverse();
    }

    CompareYear(date1, date2){
        return (date1.getFullYear() == date2.getFullYear());
    }

    CompareMonth(date1, date2){
        return (date1.getMonth() == date2.getMonth() && this.CompareYear(date1, date2));
    }

    CompareDate(date1, date2){
        return (date1.getDate() == date2.getDate() && this.CompareMonth(date1, date2));
    }

    CompareHour(date1, date2){
        return (date1.getHours() == date2.getHours() && this.CompareDate(date1, date2));
    }

    CompareMinute(date1, date2){
        return (date1.getMinutes() == date2.getMinutes() && this.CompareHour(date1, date2));
    }
}
