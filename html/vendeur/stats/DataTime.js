export class Compare {
    
    static CompareYear(date1, date2){
        return (date1.getFullYear() == date2.getFullYear());
    }

    static CompareMonth(date1, date2){
        return (date1.getMonth() == date2.getMonth() && this.CompareYear(date1, date2));
    }

    static CompareDate(date1, date2){
        return (date1.getDate() == date2.getDate() && this.CompareMonth(date1, date2));
    }

    static CompareHour(date1, date2){
        return (date1.getHours() == date2.getHours() && this.CompareDate(date1, date2));
    }

    static CompareMinute(date1, date2){
        return (date1.getMinutes() == date2.getMinutes() && this.CompareHour(date1, date2));
    }
}

export class TimeMilli {
    // valeur en millisegonde
    static UNE_SECONDE = 1000;
    static UNE_MINUTE = 60 * this.UNE_SECONDE;
    static UNE_HEURE = 60 * this.UNE_MINUTE;
    static UN_JOUR = 24 * this.UNE_HEURE;
    static UNE_SEMAINE = 7 * this.UN_JOUR;
    static UN_MOIS = 30 * this.UN_JOUR;
    static UN_ANS = 12 * this.UN_MOIS;
}

/*
"UNE_SECONDE" : 1000,
    "UNE_MINUTE" : 60 * TimeMilli.UNE_SECONDE,
    "UNE_HEURE" : 60 * TimeMilli.UNE_MINUTE,
    "UN_JOUR" : 24 * TimeMilli.UNE_HEURE,
    "UNE_SEMAINE" : 7 * TimeMilli.UN_JOUR,
    "UN_MOIS" : 30 * TimeMilli.UN_JOUR,
    "UN_ANS" : 12 * TimeMilli.UN_MOIS*/