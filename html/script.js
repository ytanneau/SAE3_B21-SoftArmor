function verif_fleches(element, nom_element) {
    if (element.scrollLeft == 0) {
        document.querySelector('.fleche.gauche.' + nom_element).style.visibility = "hidden";
        
    } else {
        document.querySelector('.fleche.gauche.' + nom_element).style.visibility = "visible";
    }
    
    if (element.scrollLeft == element.scrollWidth - element.offsetWidth) {
        document.querySelector('.fleche.droite.' + nom_element).style.visibility = "hidden";
        
    } else {
        document.querySelector('.fleche.droite.' + nom_element).style.visibility = "visible";
    }
}

function setCaroussel(nomCat) {
    console.log(nomCat);
    const cat = document.querySelector('.container.' + nomCat);
    
    console.log(nomCat);
    console.log(cat);
    document.querySelector('.fleche.gauche.' + nomCat).onclick = () => {
        cat.scrollBy({ left: -700, behavior: 'smooth' });
    };
    document.querySelector('.fleche.droite.' + nomCat).onclick = () => {
        cat.scrollBy({ left: 700, behavior: 'smooth' });
    };
    
    console.log(nomCat);
    console.log(cat);
    
    cat.addEventListener("scroll", () => {verif_fleches(cat, nomCat)});
    verif_fleches(cat, nomCat);

}

setCaroussel("recent");
setCaroussel("reduction");
setCaroussel("catalogue");