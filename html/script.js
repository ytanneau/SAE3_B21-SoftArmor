const recent = document.querySelector('.container.recent');
document.querySelector('.fleche.gauche.recent').onclick = () => {
    recent.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.recent').onclick = () => {
    recent.scrollBy({ left: 700, behavior: 'smooth' });
};


const reduction = document.querySelector('.container.reduction');
document.querySelector('.fleche.gauche.reduction').onclick = () => {
    reduction.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.reduction').onclick = () => {
    reduction.scrollBy({ left: 700, behavior: 'smooth' });
};


const alimentaire = document.querySelector('.container.alimentaire');
document.querySelector('.fleche.gauche.alimentaire').onclick = () => {
    alimentaire.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.alimentaire').onclick = () => {
    alimentaire.scrollBy({ left: 700, behavior: 'smooth' });
};

const catalogue = document.querySelector('.container.catalogue');
document.querySelector('.fleche.gauche.catalogue').onclick = () => {
    catalogue.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droite.catalogue').onclick = () => {
    catalogue.scrollBy({ left: 700, behavior: 'smooth' });
};