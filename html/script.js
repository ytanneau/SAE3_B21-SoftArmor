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

const recent = document.querySelector('.container.recent');
document.querySelector('.fleche.gauche.recent').onclick = () => {
    recent.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.recent').onclick = () => {
    recent.scrollBy({ left: 700, behavior: 'smooth' });
};

recent.addEventListener("scroll", () => {verif_fleches(recent, "recent")});
verif_fleches(recent, "recent")

const reduction = document.querySelector('.container.reduction');
document.querySelector('.fleche.gauche.reduction').onclick = () => {
    reduction.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.reduction').onclick = () => {
    reduction.scrollBy({ left: 700, behavior: 'smooth' });
};

reduction.addEventListener("scroll", () => {verif_fleches(reduction, "reduction")});
verif_fleches(reduction, "reduction")

const alimentaire = document.querySelector('.container.alimentaire');

document.querySelector('.fleche.gauche.alimentaire').onclick = () => {
    alimentaire.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.alimentaire').onclick = () => {
    alimentaire.scrollBy({ left: 700, behavior: 'smooth' });
};

alimentaire.addEventListener("scroll", () => {verif_fleches(alimentaire, "alimentaire")});
verif_fleches(alimentaire, "alimentaire")

const catalogue = document.querySelector('.container.catalogue');
document.querySelector('.fleche.gauche.catalogue').onclick = () => {
    catalogue.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.catalogue').onclick = () => {
    catalogue.scrollBy({ left: 700, behavior: 'smooth' });
};

catalogue.addEventListener("scroll", () => {verif_fleches(catalogue, "catalogue")});
verif_fleches(catalogue, "catalogue")