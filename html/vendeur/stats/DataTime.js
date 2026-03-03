export class Compare {
    
    static CompareYear(date1, date2){
        return date1.getFullYear() == date2.getFullYear();
    }

    static CompareMonth(date1, date2){
        return date1.getMonth() == date2.getMonth() && this.CompareYear(date1, date2);
    }

    static CompareDate(date1, date2){
        return date1.getDate() == date2.getDate() && this.CompareMonth(date1, date2);
    }

    static CompareHour(date1, date2){
        return date1.getHours() == date2.getHours() && this.CompareDate(date1, date2);
    }

    static CompareMinute(date1, date2){
        return date1.getMinutes() == date2.getMinutes() && this.CompareHour(date1, date2);
    }
}

export const TimeMilli = {
    // valeur en millisegonde
    "UNE_SECONDE" : 1000,
    "UNE_MINUTE" : 60 * UNE_SECONDE,
    "UNE_HEURE" : 60 * UNE_MINUTE,
    "UN_JOUR" : 24 * UNE_HEURE,
    "UNE_SEMAINE" : 7 * UN_JOUR,
    "UN_MOIS" : 30 * UN_JOUR,
    "UN_ANS" : 12 * UN_MOIS
}