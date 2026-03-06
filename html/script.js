function verif_fleches(element, nom_element) {
    if (element.scrollLeft <= 50) {
        document.querySelector('.fleche.gauche.' + nom_element).style.visibility = "hidden";
        
    } else {
        document.querySelector('.fleche.gauche.' + nom_element).style.visibility = "visible";
    }
    
    if (element.scrollLeft >= element.scrollWidth - element.offsetWidth - 50) {
        document.querySelector('.fleche.droite.' + nom_element).style.visibility = "hidden";
        
    } else {
        document.querySelector('.fleche.droite.' + nom_element).style.visibility = "visible";
    }
}

function setCaroussel(nomCat) {
    const cat = document.querySelector('.container.' + nomCat);
    
    document.querySelector('.fleche.gauche.' + nomCat).onclick = () => {
        cat.scrollBy({ left: -700, behavior: 'smooth' });
    };
    document.querySelector('.fleche.droite.' + nomCat).onclick = () => {
        cat.scrollBy({ left: 700, behavior: 'smooth' });
    };
    
    cat.addEventListener("scroll", () => {verif_fleches(cat, nomCat)});
    verif_fleches(cat, nomCat);

}

setCaroussel("recent");
setCaroussel("reduction");
setCaroussel("catalogue");